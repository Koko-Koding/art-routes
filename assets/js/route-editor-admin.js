/**
 * Route Editor Admin JavaScript
 * Handles the map modal for drawing routes and managing associated points in the WordPress admin
 */

(($) => {
	// Map variables
	let editorMap,
		drawingLayer,
		searchControl,
		routePoints = [];
	let artworkLayer, infoPointLayer;
	let isDrawing = false;
	let currentMode = "none"; // 'draw', 'addArtwork', 'addInfoPoint', 'none'
	let pointsData = { artworks: [], information_points: [] }; // Holds loaded points
	let changedPoints = { new: [], updated: [], removed: [] }; // Tracks changes
	let tempPointIdCounter = 0;
	let artworkIcon, infoPointIcon; // Custom icons

	// Store references to route point markers for easy updating/removal
	let routePointMarkers = [];

	// Localized data from PHP
	const ajaxUrl = routeEditorData.ajax_url;
	const routeId = routeEditorData.route_id;
	const getPointsNonce = routeEditorData.get_points_nonce;
	const savePointsNonce = routeEditorData.save_points_nonce;
	const i18n = routeEditorData.i18n;

	// Initialize when document is ready
	$(document).ready(() => {
		// Add modal to body if not already there
		if ($("#route-editor-modal").length === 0) {
			$("body").append(routeEditorData.modalHTML);
		}

		// Define custom icons
		artworkIcon = L.divIcon({
			className: "artwork-marker-icon",
			html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06l4.47-4.47a.75.75 0 0 1 1.06 0l3.97 3.97 3.97-3.97a.75.75 0 0 1 1.06 0l4.47 4.47V6a.75.75 0 0 0-.75-.75H3.75a.75.75 0 0 0-.75.75v10.06Z" clip-rule="evenodd" /></svg>',
			iconSize: [24, 24],
			iconAnchor: [12, 24],
			popupAnchor: [0, -24],
		});
		infoPointIcon = L.divIcon({
			className: "info-point-marker-icon",
			html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" /></svg>',
			iconSize: [24, 24],
			iconAnchor: [12, 24],
			popupAnchor: [0, -24],
		});

		// Setup event handlers
		setupEventHandlers();
	});

	/**
	 * Set up event handlers for the route editor
	 */
	function setupEventHandlers() {
		// Open the map modal
		$("#open_route_map").on("click", (e) => {
			e.preventDefault();
			openRouteEditorModal();
		});

		// Close modal
		$("body").on(
			"click",
			".route-editor-modal .close-modal, #cancel-route",
			() => {
				closeRouteEditorModal();
			},
		);

		// Close modal if clicking outside the content
		$(document).on("click", ".route-editor-modal", (e) => {
			if ($(e.target).hasClass("route-editor-modal")) {
				closeRouteEditorModal();
			}
		});

		// Drawing controls
		$("body").on("click", "#start-drawing", startDrawing);
		$("body").on("click", "#stop-drawing", stopDrawing);
		$("body").on("click", "#clear-route", clearRoute);

		// Point controls
		$("body").on("click", "#add-artwork", () => startAddingPoint("artwork"));
		$("body").on("click", "#add-info-point", () =>
			startAddingPoint("information_point"),
		);

		// Map view controls
		$("body").on("click", "#fit-route-bounds", fitRouteBounds);
		$("body").on("click", "#locate-user", locateUser);

		// Search location
		$("body").on("click", "#search-location", searchLocation);
		$("body").on("keypress", "#route-search", (e) => {
			if (e.which === 13) {
				searchLocation();
				e.preventDefault();
			}
		});

		// Save route and points
		$("body").on("click", "#save-route", saveChanges);

		// Route type change handler - recalculate duration when route type changes
		$(document).on("change", "#route_type", () => {
			updateEstimatedDuration();
		});
	}

	/**
	 * Open the route editor modal and initialize the map
	 */
	function openRouteEditorModal() {
		// Show the modal
		$("#route-editor-modal").show();

		// Initialize map if not already initialized
		if (!editorMap) {
			initEditorMap();
		} else {
			// Reset the map view if already initialized
			editorMap.invalidateSize();
		}

		// Reset state
		resetEditorState();

		// Load existing route path
		loadExistingRoutePath();

		// Load associated artworks and info points (this is async)
		// The success callback will handle the initial map view setting
		loadAssociatedPoints();
	}

	/**
	 * Close the route editor modal
	 */
	function closeRouteEditorModal() {
		$("#route-editor-modal").hide();
		stopDrawing();
		stopAddingPoint();
	}

	/**
	 * Reset editor state variables
	 */
	function resetEditorState() {
		routePoints = [];
		pointsData = { artworks: [], information_points: [] };
		changedPoints = { new: [], updated: [], removed: [] };
		tempPointIdCounter = 0;
		currentMode = "none";
		$("#save-status").text("");
		$("#adding-point-info").hide();
		$(".route-editor-controls button").removeClass("active");
		if (editorMap) {
			drawingLayer.setLatLngs([]);
			artworkLayer.clearLayers();
			infoPointLayer.clearLayers();
			// Remove temporary markers (like search highlight)
			editorMap.eachLayer((layer) => {
				if (layer.options && layer.options.isTemporary) {
					editorMap.removeLayer(layer);
				}
			});
		}
		updateRouteInfo();
	}

	/**
	 * Initialize the map for route editing
	 */
	function initEditorMap() {
		// Create the map - Default to Netherlands view initially
		editorMap = L.map("route-editor-map").setView([52.1326, 5.2913], 8);

		// Add tile layer
		L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
			attribution:
				'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
			maxZoom: 19,
		}).addTo(editorMap);

		// Add drawing layer for the route path
		drawingLayer = L.polyline([], {
			color: "#3388FF",
			weight: 4,
		}).addTo(editorMap);

		// Add layers for artwork and info points (Use FeatureGroup for getBounds)
		artworkLayer = L.featureGroup().addTo(editorMap);
		infoPointLayer = L.featureGroup().addTo(editorMap);

		// Add search control
		setupSearchControl(); // Note: setupSearchControl might call locate initially

		// Add click handler for drawing/adding points
		editorMap.on("click", onMapClick);

		// Add delegated event handler for remove links inside popups
		$(editorMap.getContainer()).on("click", ".remove-point-link", function (e) {
			e.preventDefault();
			const pointId = $(this).data("id");
			const pointType = $(this).data("type");
			removePoint(pointId, pointType);
		});

		// Handle geolocation errors - default to Netherlands view
		editorMap.on("locationerror", (e) => {
			console.warn("Geolocation failed:", e.message);
			// Only set view if it hasn't already been set by fitting bounds
			// Check if map has loaded tiles/view (simple check)
			if (!editorMap._loaded || editorMap.getZoom() === 8) {
				console.log("Setting default Netherlands view due to location error.");
				editorMap.setView([52.1326, 5.2913], 8);
			}
		});

		// Handle location found (useful if locate is called elsewhere)
		editorMap.on("locationfound", (e) => {
			// setView is handled by calling locate with setView: true
			console.log("Geolocation successful.");
		});
	}

	/**
	 * Set up the search control for finding locations
	 */
	function setupSearchControl() {
		// Use browser's Geolocation API to center on user's location
		editorMap.locate({ setView: true, maxZoom: 13 });

		// Add scale control
		L.control.scale().addTo(editorMap);
	}

	/**
	 * Handle map clicks for drawing route or adding points
	 */
	function onMapClick(e) {
		const lat = e.latlng.lat;
		const lng = e.latlng.lng;

		switch (currentMode) {
			case "draw":
				// Add point to route path
				routePoints.push([lat, lng]);
				drawingLayer.addLatLng([lat, lng]);
				updateRouteInfo();
				break;
			case "addArtwork":
			case "addInfoPoint": {
				// Add a temporary marker for a new point
				const pointType =
					currentMode === "addArtwork" ? "artwork" : "information_point";
				const tempId = `temp_${tempPointIdCounter++}`;
				const newPointData = {
					temp_id: tempId,
					lat: lat,
					lng: lng,
					type: pointType,
					title:
						(pointType === "artwork" ? i18n.artwork : i18n.infoPoint) +
						" (New)", // Temporary title
					status: "draft", // Treat new points as draft initially
				};

				addPointMarker(newPointData, true); // Pass true for isNew
				changedPoints.new.push(newPointData);
				updateRouteInfo();
				stopAddingPoint(); // Go back to 'none' mode after adding one point
				break;
			}
			default:
				// Do nothing if not in a specific mode
				break;
		}
	}

	/**
	 * Start drawing mode for the route path
	 */
	function startDrawing() {
		stopAddingPoint(); // Ensure not in adding mode
		currentMode = "draw";
		isDrawing = true;
		$("#start-drawing").addClass("active");
		$("#stop-drawing, #add-artwork, #add-info-point").removeClass("active");
		$("#drawing-instructions").text(
			'Click on the map to add points to your route. Click "Stop Drawing" when finished.',
		);
		editorMap.getContainer().style.cursor = "crosshair";
	}

	/**
	 * Stop drawing mode for the route path
	 */
	function stopDrawing() {
		if (currentMode === "draw") {
			currentMode = "none";
			isDrawing = false;
			$("#start-drawing").removeClass("active");
			$("#stop-drawing").addClass("active");
			$("#drawing-instructions").text(
				'Drawing paused. Click "Start Drawing" to continue, or add points of interest.',
			);
			editorMap.getContainer().style.cursor = "";
		}
	}

	/**
	 * Start mode for adding an artwork or info point
	 */
	function startAddingPoint(type) {
		stopDrawing(); // Ensure not in drawing mode
		currentMode = type === "artwork" ? "addArtwork" : "addInfoPoint";
		const typeLabel = type === "artwork" ? i18n.artwork : i18n.infoPoint;

		$("#add-artwork, #add-info-point").removeClass("active");
		$(type === "artwork" ? "#add-artwork" : "#add-info-point").addClass(
			"active",
		);
		$("#start-drawing, #stop-drawing").removeClass("active");

		$("#adding-point-info")
			.text(`Click on the map to place the new ${typeLabel}.`)
			.show();
		$("#drawing-instructions").hide();
		editorMap.getContainer().style.cursor = "copy";
	}

	/**
	 * Stop mode for adding points
	 */
	function stopAddingPoint() {
		if (currentMode === "addArtwork" || currentMode === "addInfoPoint") {
			currentMode = "none";
			$("#add-artwork, #add-info-point").removeClass("active");
			$("#adding-point-info").hide();
			$("#drawing-instructions")
				.show()
				.text(
					"Select an action: Start Drawing, Add Artwork, or Add Info Point.",
				);
			editorMap.getContainer().style.cursor = "";
		}
	}

	/**
	 * Clear the current route path
	 */
	function clearRoute() {
		if (!confirm("Are you sure you want to clear the entire route path?"))
			return;

		routePoints = [];
		drawingLayer.setLatLngs([]);
		updateRouteInfo();
		$("#drawing-instructions").text(
			'Route path cleared. Click "Start Drawing" to begin a new path.',
		);
	}

	/**
	 * Search for a location and center the map on it
	 */
	function searchLocation() {
		const searchValue = $("#route-search").val().trim();

		if (!searchValue) return;

		// Show loading indicator
		$("#search-location").text("Searching...").prop("disabled", true);

		// Use Nominatim for geocoding (OpenStreetMap's service)
		$.ajax({
			url: "https://nominatim.openstreetmap.org/search",
			type: "GET",
			data: {
				q: searchValue,
				format: "json",
				limit: 1,
			},
			dataType: "json",
			success: (data) => {
				if (data && data.length > 0) {
					const result = data[0];
					const lat = parseFloat(result.lat);
					const lon = parseFloat(result.lon);

					// Center map on result
					editorMap.setView([lat, lon], 14);

					// Add a temporary highlight
					const searchHighlight = L.circle([lat, lon], {
						color: "#FF5722",
						fillColor: "#FF5722",
						fillOpacity: 0.5,
						radius: 50,
						isTemporary: true,
					}).addTo(editorMap);

					// Remove highlight after 3 seconds
					setTimeout(() => {
						editorMap.removeLayer(searchHighlight);
					}, 3000);
				} else {
					alert("Location not found. Please try a different search term.");
				}
			},
			error: () => {
				alert("Error searching for location. Please try again.");
			},
			complete: () => {
				// Reset button
				$("#search-location").text("Search").prop("disabled", false);
			},
		});
	}

	/**
	 * Fit map bounds to show the entire route and all points
	 */
	function fitRouteBounds() {
		if (!editorMap) return;

		const bounds = L.latLngBounds([]);

		// Include route path bounds
		if (drawingLayer && drawingLayer.getLatLngs().length > 0) {
			bounds.extend(drawingLayer.getBounds());
		}

		// Include artwork bounds
		if (artworkLayer && artworkLayer.getLayers().length > 0) {
			bounds.extend(artworkLayer.getBounds());
		}

		// Include info point bounds
		if (infoPointLayer && infoPointLayer.getLayers().length > 0) {
			bounds.extend(infoPointLayer.getBounds());
		}

		if (bounds.isValid()) {
			console.log("Fitting map to route/points bounds.");
			editorMap.fitBounds(bounds, { padding: [50, 50], maxZoom: 17 });
		} else {
			console.log("Fit Route Bounds: No valid bounds found (no route/points).");
		}
	}

	/**
	 * Attempt to locate the user and center the map
	 */
	function locateUser() {
		console.log("Attempting user geolocation.");
		if (!editorMap) return;
		console.log("Attempting geolocation via 'My Location' button.");
		editorMap.locate({ setView: true, maxZoom: 16 });
	}

	/**
	 * Save all changes (route path and points)
	 */
	function saveChanges() {
		// Format route path points for storage as JSON (save all properties)
		const formattedPath = JSON.stringify(
			routePoints.map((pt) => {
				if (
					typeof pt === "object" &&
					pt !== null &&
					pt.lat !== undefined &&
					pt.lng !== undefined
				) {
					// Only include relevant properties
					return {
						lat: pt.lat,
						lng: pt.lng,
						is_start: !!pt.is_start,
						is_end: !!pt.is_end,
						notes: pt.notes || "",
						arrow_direction: pt.arrow_direction || null,
					};
				} else if (Array.isArray(pt) && pt.length >= 2) {
					// Fallback for old format
					return { lat: pt[0], lng: pt[1] };
				}
			}),
			null,
			2,
		);

		// Prepare data for AJAX
		const dataToSend = {
			action: "save_route_points",
			nonce: savePointsNonce,
			route_id: routeId,
			route_path: formattedPath,
			route_length: calculateRouteLength(),
			new_points: changedPoints.new,
			updated_points: changedPoints.updated,
			removed_points: changedPoints.removed,
		};

		// Show saving status
		$("#save-status").text(i18n.savingPoints).css("color", "orange");
		$("#save-route").prop("disabled", true);

		$.ajax({
			url: ajaxUrl,
			type: "POST",
			data: dataToSend,
			dataType: "json",
			success: (response) => {
				if (response.success) {
					$("#save-status").text(i18n.pointsSaved).css("color", "green");

					// Update the main route path textarea in the underlying page
					$("#route_path").val(formattedPath.trim());
					// Update route length field if available
					if ($("#route_length").length) {
						$("#route_length").val(calculateRouteLength().toFixed(2));
					}

					// Process newly added points (update temp IDs with real IDs)
					if (response.data.added && response.data.added.length > 0) {
						response.data.added.forEach((addedPoint) => {
							const layer =
								addedPoint.type === "artwork" ? artworkLayer : infoPointLayer;
							layer.eachLayer((marker) => {
								if (
									marker.options.pointData &&
									marker.options.pointData.temp_id === addedPoint.temp_id
								) {
									// Update marker's internal data
									marker.options.pointData.id = addedPoint.new_id;
									marker.options.pointData.edit_link = addedPoint.edit_link;
									delete marker.options.pointData.temp_id;
									// Update popup content
									marker.setPopupContent(
										createPointPopupContent(marker.options.pointData),
									);
								}
							});
						});
					}

					// Reset change tracking
					changedPoints = { new: [], updated: [], removed: [] };

					// Optionally close modal after a delay
					// setTimeout(closeRouteEditorModal, 1500);
				} else {
					console.error("Save Error:", response.data.message);
					$("#save-status")
						.text(
							i18n.errorSavingPoints +
								(response.data.message ? `: ${response.data.message}` : ""),
						)
						.css("color", "red");
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				console.error("AJAX Save Error:", textStatus, errorThrown);
				$("#save-status")
					.text(`${i18n.errorSavingPoints} (AJAX: ${textStatus})`)
					.css("color", "red");
			},
			complete: () => {
				$("#save-route").prop("disabled", false);
				// Clear status message after a few seconds
				setTimeout(() => {
					$("#save-status").text("");
				}, 5000);
			},
		});
	}

	/**
	 * Calculate estimated duration based on route type and distance
	 */
	function calculateEstimatedDuration(distanceKm, routeType) {
		if (!distanceKm || distanceKm <= 0) {
			return 0;
		}

		// Average speeds in km/h for different route types
		const speeds = {
			'walking': 4.5,      // Average walking speed
			'cycling': 15,       // Average cycling speed  
			'wheelchair': 3.5,   // Slower walking speed for wheelchair accessibility
			'children': 3.0      // Slower pace for child-friendly routes
		};

		// Default to walking speed if route type not specified or unknown
		const speed = speeds[routeType] || speeds['walking'];
		
		// Calculate duration in hours, then convert to minutes
		const durationHours = distanceKm / speed;
		const durationMinutes = Math.round(durationHours * 60);

		console.log(`Duration calculation: ${distanceKm.toFixed(2)}km at ${speed}km/h = ${durationMinutes} minutes (route type: ${routeType || 'default/walking'})`);

		return durationMinutes;
	}

	/**
	 * Update duration field based on current distance and route type
	 */
	function updateEstimatedDuration() {
		const distance = calculateRouteLength();
		const routeTypeField = $("#route_type");
		const durationField = $("#route_duration");
		
		if (distance > 0 && routeTypeField.length && durationField.length) {
			const routeType = routeTypeField.val();
			const estimatedDuration = calculateEstimatedDuration(distance, routeType);
			
			// Only update if the duration field is empty or if it's being calculated automatically
			const currentDuration = durationField.val();
			if (!currentDuration || currentDuration == 0) {
				durationField.val(estimatedDuration);
				console.log(`Auto-updated duration field to ${estimatedDuration} minutes`);
			}
		}
	}

	/**
	 * Calculate the total route length in kilometers
	 */
	function calculateRouteLength() {
		let totalDistance = 0;

		for (let i = 0; i < routePoints.length - 1; i++) {
			totalDistance += calculateDistance(
				routePoints[i]?.lat,
				routePoints[i]?.lng,
				routePoints[i + 1]?.lat,
				routePoints[i + 1]?.lng,
			);
		}

		// Convert meters to kilometers
		return totalDistance / 1000;
	}

	/**
	 * Calculate distance between two points using Haversine formula
	 */
	function calculateDistance(lat1, lon1, lat2, lon2) {
		const R = 6371e3; // Earth radius in meters
		const φ1 = (lat1 * Math.PI) / 180;
		const φ2 = (lat2 * Math.PI) / 180;
		const Δφ = ((lat2 - lat1) * Math.PI) / 180;
		const Δλ = ((lon2 - lon1) * Math.PI) / 180;

		const a =
			Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
			Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
		const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

		return R * c; // in meters
	}

	/**
	 * Update the route information display (counts, distance)
	 */
	function updateRouteInfo() {
		// Route path points
		$("#point-count").text(routePoints.length);
		const distance = calculateRouteLength();
		$("#route-distance").text(distance.toFixed(2));

		// Update estimated duration when distance changes
		updateEstimatedDuration();

		// Artwork and Info Point counts (consider current state including changes)
		let artworkCount = pointsData.artworks.filter(
			(p) => !changedPoints.removed.includes(p.id),
		).length;
		artworkCount += changedPoints.new.filter(
			(p) => p.type === "artwork",
		).length;

		let infoPointCount = pointsData.information_points.filter(
			(p) => !changedPoints.removed.includes(p.id),
		).length;
		infoPointCount += changedPoints.new.filter(
			(p) => p.type === "information_point",
		).length;

		$("#artwork-count").text(artworkCount);
		$("#info-point-count").text(infoPointCount);

		// Draw draggable markers for each route path point
		drawRoutePointMarkers();
	}

	/**
	 * Draw draggable markers for each route path point
	 */
	function drawRoutePointMarkers() {
		// Remove old markers
		routePointMarkers.forEach((marker) => editorMap.removeLayer(marker));
		routePointMarkers = [];
		if (!routePoints || routePoints.length === 0) return;
		routePoints.forEach((pt, idx) => {
			// Support new metadata: is_start, is_end, notes
			const pointObj =
				typeof pt === "object" &&
				pt !== null &&
				pt.lat !== undefined &&
				pt.lng !== undefined
					? pt
					: { lat: pt[0], lng: pt[1] };
			// Visual indicator for start/end
			let markerLabel = "";
			if (pointObj.is_start)
				markerLabel =
					'<span title="Start" style="position:absolute;left:0;top:-18px;font-size:12px;color:#388e3c;">&#9679;</span>';
			if (pointObj.is_end)
				markerLabel =
					'<span title="End" style="position:absolute;right:0;top:-18px;font-size:12px;color:#d32f2f;">&#9679;</span>';

			// Arrow indicator for direction
			let arrowIndicator = "";
			if (
				pointObj.arrow_direction !== null &&
				pointObj.arrow_direction !== undefined &&
				pointObj.arrow_direction !== ""
			) {
				const direction = parseFloat(pointObj.arrow_direction);
				if (!isNaN(direction)) {
					arrowIndicator = `<div title="Arrow direction: ${direction}°" style="position:absolute;top:-28px;left:50%;transform:translateX(-50%) rotate(${direction}deg);width:0;height:0;border-left:3px solid transparent;border-right:3px solid transparent;border-bottom:12px solid #ff6b35;"></div>`;
				}
			}
			const iconHtml = `
                <div class="route-point-marker-dot" style="position: relative; width: 18px; height: 18px;">
                    <div style="width: 14px; height: 14px; background: #3388FF; border: 2px solid #fff; border-radius: 50%; position: absolute; left: 2px; top: 2px;"></div>
                    <div class="route-point-button-bar" style="position: absolute; transform: translateX(100%); right: -0px; top: 1px; display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 0.125rem; background: rgba(255, 255, 255, 0.8); padding: 2px; border-radius: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.2);">
                        <button class="route-point-edit-btn" title="Edit this route point" style="width: 18px; height: 18px; border: none; background: #1976d2; color: #fff; border-radius: 50%; font-size: 14px; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center;">✎</button>
                        <button class="route-point-delete-btn" title="Delete this route point" style="width: 18px; height: 18px; border: none; background: #e53935; color: #fff; border-radius: 50%; font-size: 14px; cursor: pointer; z-index: 10; display: flex; align-items: center; justify-content: center;">&times;</button>
                    </div>
                    ${markerLabel}
                    ${arrowIndicator}
                </div>
            `;
			const marker = L.marker([pointObj.lat, pointObj.lng], {
				icon: L.divIcon({
					className: "route-point-marker",
					html: iconHtml,
					iconSize: [18, 18],
					iconAnchor: [9, 9],
				}),
				draggable: true,
			}).addTo(editorMap);
			marker._routeIdx = idx;
			// Drag handler
			marker.on("drag", (e) => {
				const newLatLng = e.target.getLatLng();
				if (
					typeof routePoints[idx] === "object" &&
					routePoints[idx] !== null &&
					routePoints[idx].lat !== undefined
				) {
					routePoints[idx].lat = newLatLng.lat;
					routePoints[idx].lng = newLatLng.lng;
				} else {
					routePoints[idx] = [newLatLng.lat, newLatLng.lng];
				}
				drawingLayer.setLatLngs(
					routePoints.map((pt) =>
						pt.lat !== undefined ? [pt.lat, pt.lng] : pt,
					),
				);
			});
			marker.on("dragend", (e) => {
				$("#save-status").text("Unsaved changes").css("color", "orange");
			});
			// Delete button handler
			setTimeout(() => {
				const btn =
					marker._icon && marker._icon.querySelector(".route-point-delete-btn");
				if (btn) {
					btn.addEventListener("click", (ev) => {
						ev.stopPropagation();
						if (routePoints.length <= 2) {
							alert(
								i18n.cannotDeleteLastPoints ||
									"A route must have at least two points.",
							);
							return;
						}
						if (
							confirm(
								i18n.confirmDeleteRoutePoint || "Delete this route point?",
							)
						) {
							routePoints.splice(idx, 1);
							drawingLayer.setLatLngs(
								routePoints.map((pt) =>
									pt.lat !== undefined ? [pt.lat, pt.lng] : pt,
								),
							);
							updateRouteInfo();
							$("#save-status").text("Unsaved changes").css("color", "orange");
						}
					});
				}
				// Edit button handler
				const editBtn =
					marker._icon && marker._icon.querySelector(".route-point-edit-btn");
				if (editBtn) {
					editBtn.addEventListener("click", (ev) => {
						ev.stopPropagation();
						showRoutePointEditModal(idx);
					});
				}
			}, 0);
			routePointMarkers.push(marker);
		});
	}

	/**
	 * Load existing route path from textarea
	 */
	function loadExistingRoutePath() {

		const routeText = $("#route_path").val().trim();
		routePoints = []; // Reset points

		if (!routeText) {
			drawingLayer.setLatLngs([]);
			updateRouteInfo();
			return;
		}

		let parsed = null;
		// Try to parse as JSON
		try {
			parsed = JSON.parse(routeText);
		} catch (e) {
			parsed = null;
		}

		if (
			Array.isArray(parsed) &&
			parsed.length > 0 &&
			typeof parsed[0] === "object" &&
			parsed[0].lat !== undefined &&
			parsed[0].lng !== undefined
		) {
			// New JSON format: preserve all properties
			routePoints = parsed.map((pt) => ({
				lat: parseFloat(pt.lat),
				lng: parseFloat(pt.lng),
				is_start: !!pt.is_start,
				is_end: !!pt.is_end,
				notes: pt.notes || "",
				arrow_direction: pt.arrow_direction || null,
			}));
		} else {
			// Old format: lines of lat, lng
			const lines = routeText.split("\n");
			const validPoints = [];
			lines.forEach((line) => {
				const parts = line.trim().split(",");
				if (parts.length >= 2) {
					const lat = parseFloat(parts[0].trim());
					const lng = parseFloat(parts[1].trim());
					if (!isNaN(lat) && !isNaN(lng)) {
						validPoints.push({ lat, lng });
					}
				}
			});
			routePoints = validPoints;
			// Auto-migrate: update textarea to JSON format
			if (validPoints.length > 0) {
				$("#route_path").val(JSON.stringify(validPoints, null, 2));
			}
		}

		drawingLayer.setLatLngs(routePoints.map((pt) => [pt.lat, pt.lng]));
		
		updateRouteInfo();
		drawRoutePointMarkers();
	}

	/**
	 * Load associated artworks and info points via AJAX
	 */
	function loadAssociatedPoints() {
		if (!routeId) {
			// No route ID, attempt geolocation as fallback if map exists
			if (editorMap) {
				console.log("No route ID, attempting geolocation.");
				editorMap.locate({ setView: true, maxZoom: 14 });
			}
			return;
		}

		$.ajax({
			url: ajaxUrl,
			type: "POST",
			data: {
				action: "get_route_points",
				nonce: getPointsNonce,
				route_id: routeId,
			},
			dataType: "json",
			success: (response) => {
				if (response.success) {
					pointsData = response.data; // Store loaded points
					displayPoints(pointsData.artworks, "artwork");
					displayPoints(pointsData.information_points, "information_point");
					updateRouteInfo();

					// --- Start: Set Initial Map View Logic ---
					fitRouteBounds();
					// --- End: Set Initial Map View Logic ---
				} else {
					console.error("Error loading points:", response.data.message);
					alert(
						i18n.errorLoadingPoints +
							(response.data.message ? `: ${response.data.message}` : ""),
					);
					// Fallback if loading points fails: attempt geolocation
					if (editorMap) {
						console.log("Error loading points, attempting geolocation.");
						locateUser();
					}
				}
			},
			error: (jqXHR, textStatus, errorThrown) => {
				console.error("AJAX Load Points Error:", textStatus, errorThrown);
				alert(`${i18n.errorLoadingPoints} (AJAX: ${textStatus})`);
				// Fallback if AJAX fails: attempt geolocation
				if (editorMap) {
					console.log("AJAX error loading points, attempting geolocation.");
					locateUser();
				}
			},
		});
	}

	/**
	 * Display markers for artworks or info points
	 */
	function displayPoints(points, type) {
		const layer = type === "artwork" ? artworkLayer : infoPointLayer;

		points.forEach((point) => {
			addPointMarker(point);
		});
	}

	/**
	 * Add a single marker for an artwork or info point
	 */
	function addPointMarker(pointData, isNew = false) {
		const layer = pointData.type === "artwork" ? artworkLayer : infoPointLayer;
		const icon = pointData.type === "artwork" ? artworkIcon : infoPointIcon;
		const isDraft = pointData.status === "draft";

		const marker = L.marker([pointData.lat, pointData.lng], {
			icon: icon,
			draggable: true,
			pointData: { ...pointData }, // Store data within the marker
			// Add a class if the point is a draft
			iconOptions: { className: isDraft ? "draft-point" : "" },
		}).addTo(layer);

		// Apply the draft class to the actual icon element after creation
		if (isDraft && marker._icon) {
			L.DomUtil.addClass(marker._icon, "draft-point");
		}

		marker.bindPopup(createPointPopupContent(pointData));

		// Handle dragging
		marker.on("dragend", (e) => {
			const movedMarker = e.target;
			const newLatLng = movedMarker.getLatLng();
			const markerData = movedMarker.options.pointData;

			// Update internal data
			markerData.lat = newLatLng.lat;
			markerData.lng = newLatLng.lng;

			// Mark as updated (if it's not a new, unsaved point)
			if (markerData.id) {
				// Check if already in updated list
				const existingUpdateIndex = changedPoints.updated.findIndex(
					(p) => p.id === markerData.id,
				);
				if (existingUpdateIndex > -1) {
					changedPoints.updated[existingUpdateIndex].lat = newLatLng.lat;
					changedPoints.updated[existingUpdateIndex].lng = newLatLng.lng;
				} else {
					changedPoints.updated.push({
						id: markerData.id,
						lat: newLatLng.lat,
						lng: newLatLng.lng,
					});
				}
			} else if (markerData.temp_id) {
				// If it's a new point, update its data in the new array
				const newPointIndex = changedPoints.new.findIndex(
					(p) => p.temp_id === markerData.temp_id,
				);
				if (newPointIndex > -1) {
					changedPoints.new[newPointIndex].lat = newLatLng.lat;
					changedPoints.new[newPointIndex].lng = newLatLng.lng;
				}
			}
			$("#save-status").text("Unsaved changes").css("color", "orange"); // Indicate unsaved changes
		});
	}

	/**
	 * Create HTML content for a point marker's popup
	 */
	function createPointPopupContent(pointData) {
		const isDraft = pointData.status === "draft";
		let content = `<strong>${pointData.title || "Point"}${isDraft ? " (Draft)" : ""}</strong><br>`;
		content += `Type: ${pointData.type === "artwork" ? i18n.artwork : i18n.infoPoint}<br>`;
		content += `Lat: ${pointData.lat.toFixed(5)}, Lng: ${pointData.lng.toFixed(5)}<br>`;
		if (isDraft) {
			content += `<span style="color: orange; font-weight: bold;">${i18n.draftWarning || "Warning: This point is a draft and won't be visible on the public map."}</span><br>`;
		}

		if (pointData.id) {
			// Only show edit link for saved points
			content += `<a href="${pointData.edit_link}" target="_blank">${i18n.edit}</a> | `;
		}
		content += `<a href="#" class="remove-point-link" data-id="${pointData.id || pointData.temp_id}" data-type="${pointData.type}">${i18n.remove}</a>`;
		return content;
	}

	/**
	 * Remove an artwork or info point marker and track removal
	 */
	function removePoint(pointId, pointType) {
		if (!confirm(i18n.confirmRemove)) return;

		const layer = pointType === "artwork" ? artworkLayer : infoPointLayer;
		let markerToRemove = null;

		layer.eachLayer((marker) => {
			if (
				marker.options.pointData &&
				(marker.options.pointData.id === pointId ||
					marker.options.pointData.temp_id === pointId)
			) {
				markerToRemove = marker;
			}
		});

		if (markerToRemove) {
			layer.removeLayer(markerToRemove);

			// Check if it was a new, unsaved point
			const newPointIndex = changedPoints.new.findIndex(
				(p) => p.temp_id === pointId,
			);
			if (newPointIndex > -1) {
				// Just remove it from the new points array
				changedPoints.new.splice(newPointIndex, 1);
			} else if (pointId && typeof pointId !== "string") {
				// Check if it's a real ID (not temp_...)
				// Add to removed points array if not already there
				if (!changedPoints.removed.includes(pointId)) {
					changedPoints.removed.push(pointId);
				}
				// If it was also marked as updated, remove from updated array
				const updatedIndex = changedPoints.updated.findIndex(
					(p) => p.id === pointId,
				);
				if (updatedIndex > -1) {
					changedPoints.updated.splice(updatedIndex, 1);
				}
			}

			updateRouteInfo();
			$("#save-status").text("Unsaved changes").css("color", "orange"); // Indicate unsaved changes
		}
	}

	// Add modal HTML for editing route point metadata
	if ($("#route-point-edit-modal").length === 0) {
		$("body").append(`
        <div id="route-point-edit-modal" class="route-point-edit-modal">
            <div class="route-point-edit-content">
                <button id="close-route-point-edit-modal" style="position:absolute;top:8px;right:8px;background:none;border:none;font-size:20px;cursor:pointer;">&times;</button>
                <h3 style="margin-top:0;font-size:1.1em;">Edit Route Point</h3>
                <form id="route-point-edit-form" class="route-point-edit-form">
                    <div style="margin-bottom:8px;">
                        <label><input type="checkbox" name="is_start"> Start point</label>
                        <label style="margin-left:12px;"><input type="checkbox" name="is_end"> End point</label>
                    </div>
                    <div style="margin-bottom:8px;">
                        <label>Arrow Direction (0-360°):<br>
                            <input type="number" name="arrow_direction" min="0" max="360" step="1" style="width:80px;" placeholder="None" />
                            <span style="font-size:0.9em;color:#666;margin-left:8px;">Leave empty for no arrow</span>
                        </label>
                    </div>
                    <div style="margin-bottom:8px;">
                        <label>Notes:<br><textarea name="notes" rows="2" style="width:100%;resize:vertical;"></textarea></label>
                    </div>
                    <div id="info-point-icon-field" style="margin-bottom:8px;display:none;">
                        <label>Icon image:<br>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <img id="info-point-icon-preview" src="" alt="" style="max-width:48px;max-height:48px;display:none;border:1px solid #ccc;" />
                                <button type="button" id="select-info-point-icon" class="button">Select Image</button>
                                <button type="button" id="remove-info-point-icon" class="button" style="display:none;">Remove</button>
                            </div>
                            <input type="hidden" name="icon_url" value="" />
                        </label>
                    </div>
                    <button type="submit" style="background:#1976d2;color:#fff;border:none;padding:6px 16px;border-radius:4px;">Save</button>
                </form>
            </div>
        </div>
`);

		// Media library integration for icon image
		let infoPointIconFrame;
		$(document).on("click", "#select-info-point-icon", (e) => {
			e.preventDefault();
			if (infoPointIconFrame) {
				infoPointIconFrame.open();
				return;
			}
			infoPointIconFrame = wp.media({
				title: "Select Icon Image",
				button: { text: "Use this image" },
				multiple: false,
			});
			infoPointIconFrame.on("select", () => {
				const attachment = infoPointIconFrame
					.state()
					.get("selection")
					.first()
					.toJSON();
				$("#info-point-icon-preview").attr("src", attachment.url).show();
				$("#route-point-edit-form [name='icon_url']").val(attachment.url);
				$("#remove-info-point-icon").show();
			});
			infoPointIconFrame.open();
		});
		$(document).on("click", "#remove-info-point-icon", function (e) {
			e.preventDefault();
			$("#info-point-icon-preview").attr("src", "").hide();
			$("#route-point-edit-form [name='icon_url']").val("");
			$(this).hide();
		});
	}

	// Show modal and populate with current data
	function showRoutePointEditModal(idx) {
		const pt = routePoints[idx];
		const isObj = typeof pt === "object" && pt !== null;
		const modal = $("#route-point-edit-modal");
		const form = $("#route-point-edit-form");
		form[0].reset();
		// Show/hide icon field for info points only
		if (isObj && pt.type === "information_point") {
			$("#info-point-icon-field").show();
			// Set preview and value if present
			if (pt.icon_url) {
				$("#info-point-icon-preview").attr("src", pt.icon_url).show();
				form.find('[name="icon_url"]').val(pt.icon_url);
				$("#remove-info-point-icon").show();
			} else {
				$("#info-point-icon-preview").attr("src", "").hide();
				form.find('[name="icon_url"]').val("");
				$("#remove-info-point-icon").hide();
			}
		} else {
			$("#info-point-icon-field").hide();
		}
		if (isObj) {
			form.find('[name="is_start"]').prop("checked", !!pt.is_start);
			form.find('[name="is_end"]').prop("checked", !!pt.is_end);
			form.find('[name="notes"]').val(pt.notes || "");
			form.find('[name="arrow_direction"]').val(pt.arrow_direction || "");
		}
		// Show as flex
		modal.css({
			display: "flex",
		});
		// Save handler
		form.off("submit").on("submit", (e) => {
			e.preventDefault();
			if (isObj) {
				pt.is_start = form.find('[name="is_start"]').is(":checked");
				pt.is_end = form.find('[name="is_end"]').is(":checked");
				pt.notes = form.find('[name="notes"]').val();
				pt.arrow_direction = form.find('[name="arrow_direction"]').val();
				if (pt.type === "information_point") {
					pt.icon_url = form.find('[name="icon_url"]').val();
				}
			}
			modal.hide();
			updateRouteInfo();
			$("#save-status").text("Unsaved changes").css("color", "orange");
		});
		// Close handler
		$("#close-route-point-edit-modal")
			.off("click")
			.on("click", () => {
				modal.hide();
			});
		// Hide modal on background click
		modal.off("click").on("click", function (e) {
			if (e.target === this) modal.hide();
		});
	}
})(jQuery);
