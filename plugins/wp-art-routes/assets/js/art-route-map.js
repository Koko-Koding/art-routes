/**
 * Art Route Map JavaScript
 * Handles the OpenStreetMap integration, location tracking, and artwork/info point markers
 */

(($) => {
	// Marker display order for zIndexOffset
	const markerDisplayOrder = {
		ROUTE_START: 3000,
		ROUTE_END: 3000,
		ARTWORK: 2000,
		INFO_POINT: 1000,
		DIRECTION_ARROW: 500,
		USER: 1000,
	};

	// Map variables
	let map, userMarker, routeLayer, completedRouteLayer, userToRouteLayer;
	let userPosition = null;
	let watchId = null;
	const artworkMarkers = [];
	const infoPointMarkers = [];
	let routePath = [];
	let completedPath = [];
	let initialLocationSet = false;
	// Add toast container and queue
	const toastQueue = [];
	let toastDisplaying = false;
	// Flag to indicate whether to show completed route
	let showCompletedRoute = true;
	// Flag to indicate whether to show artwork toasts
	let showArtworkToasts = true;

	// Visibility state for map elements
	const visibilityState = {
		artworks: true,
		infoPoints: true,
		route: true,
		userLocation: true,
	};

	// Initialize the map when the DOM is ready
	$(document).ready(() => {
		initMap();
		// Create toast container
		$("body").append(
			'<div id="art-route-toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>',
		);

		// Initialize map controls
		initMapControls();
	});

	/**
	 * Initialize the OpenStreetMap with Leaflet
	 */
	function initMap() {
		// Check if the map container exists
		if (!document.getElementById("art-route-map")) {
			return;
		}

		// Show loading indicator
		$("#map-loading").show();

		// Create the map centered on default position (will be updated with user's position)
		map = L.map("art-route-map").setView([52.1326, 5.2913], 13); // Default center on Netherlands

		// Add the OpenStreetMap tile layer
		L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
			attribution:
				'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
			maxZoom: 19,
		}).addTo(map);

		// Get route data from WordPress
		routePath = artRouteData.route_path;

		// Check if we should show the completed route
		showCompletedRoute =
			artRouteData.show_completed_route !== undefined
				? artRouteData.show_completed_route
				: true;

		// Check if we should show artwork toasts
		showArtworkToasts =
			artRouteData.show_artwork_toasts !== undefined
				? artRouteData.show_artwork_toasts
				: true;

		// Add the route to the map
		drawRoute();
		addStartEndMarkers();

		// Add artwork markers
		addArtworkMarkers();

		// Add information point markers
		addInfoPointMarkers();

		// Get user location
		getUserLocation();

		// Retry location button
		$("#retry-location").on("click", () => {
			$("#location-error").hide();
			getUserLocation();
		});

		// Hide loading indicator
		$("#map-loading").hide();
	}

	/**
	 * Get the user's current location
	 */
	function getUserLocation() {
		if (navigator.geolocation) {
			// Hide any previous errors
			$("#location-error").hide();

			// Watch the user's position and update in real-time
			watchId = navigator.geolocation.watchPosition(
				updateUserPosition,
				handleLocationError,
				{
					enableHighAccuracy: true,
					maximumAge: 10000,
					timeout: 10000,
				},
			);
		} else {
			// Geolocation not supported
			$("#location-error")
				.show()
				.find("p")
				.text("Je browser ondersteunt geen locatievoorzieningen.");
		}
	}

	/**
	 * Update the user's position on the map
	 */
	function updateUserPosition(position) {
		const latitude = position.coords.latitude;
		const longitude = position.coords.longitude;

		// Store current position
		userPosition = [latitude, longitude];

		// Create or update user marker
		if (!userMarker) {
			// Create a custom marker for the user
			const userIcon = L.divIcon({
				className: "user-marker",
				html: '<div class="user-marker-inner"></div>',
				iconSize: [20, 20],
				iconAnchor: [10, 10],
			});

			userMarker = L.marker([latitude, longitude], {
				icon: userIcon,
				zIndexOffset: 1000,
			}).addTo(map);

			// Do NOT fit bounds to combined route and user location here
			// Only center on user if no route is present
			if (!routeLayer) {
				map.setView([latitude, longitude], 19);
			}

			// Set flag for initial location
			initialLocationSet = true;
		} else {
			// Update existing marker
			userMarker.setLatLng([latitude, longitude]);
		}

		// Update completed route path if feature is enabled
		if (showCompletedRoute) {
			updateCompletedRoute();
		}

		// Only check artwork proximity if not first location update
		if (initialLocationSet) {
			checkArtworkProximity();
		}
	}

	/**
	 * Handle location errors
	 */
	function handleLocationError(error) {
		let errorMessage;

		switch (error.code) {
			case error.PERMISSION_DENIED:
				errorMessage =
					"Locatietoegang is geweigerd. Sta locatietoegang toe in je browser.";
				break;
			case error.POSITION_UNAVAILABLE:
				errorMessage = "Locatie-informatie is niet beschikbaar.";
				break;
			case error.TIMEOUT:
				errorMessage = "Het verzoek om je locatie is verlopen.";
				break;
			case error.UNKNOWN_ERROR:
				errorMessage =
					"Er is een onbekende fout opgetreden bij het ophalen van je locatie.";
				break;
		}

		// Show error message
		$("#location-error").show().find("p").text(errorMessage);
	}

	/**
	 * Draw the route on the map
	 */
	function drawRoute() {
		if (routePath && routePath.length > 0) {
			// Create a polyline for the route
			routeLayer = L.polyline(routePath, {
				color: "#3388FF",
				weight: 4,
				opacity: 0.7,
				lineJoin: "round",
			}).addTo(map);

			// Fit the map to the route bounds (only initially if user location not yet available)
			if (!userPosition) {
				map.fitBounds(routeLayer.getBounds(), {
					padding: [50, 50],
					maxZoom: 16, // Limit how far it can zoom out
				});
			}

			// Initialize completed route layer (empty at first) if feature is enabled
			if (showCompletedRoute) {
				completedRouteLayer = L.polyline([], {
					color: "#32CD32", // Green color for completed segments
					weight: 4,
					opacity: 0.8,
					lineJoin: "round",
				}).addTo(map);

				// Create a separate layer for the line connecting user to route
				userToRouteLayer = L.polyline([], {
					color: "#32CD32", // Same base color as completed route but different style
					weight: 3,
					opacity: 0.5, // More transparent
					dashArray: "6, 8", // Dashed line
					lineJoin: "round",
				}).addTo(map);
			}
		}
	}

	// Add start/end point markers after drawing the route
	function addStartEndMarkers() {
		if (!routePath || !routePath.length) return;
		routePath.forEach((pt, idx) => {
			let lat, lng, isStart, isEnd, notes, arrowDirection;
			if (Array.isArray(pt)) {
				lat = pt[0];
				lng = pt[1];
				isStart = false;
				isEnd = false;
				notes = "";
				arrowDirection = null;
			} else {
				lat = pt.lat;
				lng = pt.lng;
				isStart = !!pt.is_start;
				isEnd = !!pt.is_end;
				notes = pt.notes || "";
				arrowDirection = pt.arrow_direction || null;
			}

			// Show start/end markers
			if (isStart || isEnd) {
				const iconHtml = isStart
					? '<div style="background:#388e3c;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;"><svg width="18" height="18" viewBox="0 0 24 24" fill="#fff"><path d="M6 2v20l15-10L6 2z"/></svg></div>'
					: '<div style="background:#d32f2f;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;"><svg width="18" height="18" viewBox="0 0 24 24" fill="#fff"><path d="M18 2v20L3 12 18 2z"/></svg></div>';
				const marker = L.marker([lat, lng], {
					icon: L.divIcon({
						className: isStart ? "route-start-marker" : "route-end-marker",
						html: iconHtml,
						iconSize: [28, 28],
						iconAnchor: [14, 14],
					}),
					zIndexOffset: isStart
						? markerDisplayOrder.ROUTE_START
						: markerDisplayOrder.ROUTE_END,
				}).addTo(map);
				let popupHtml =
					'<div class="route-point-popup-container"><div class="route-point-popup-content">';
				popupHtml +=
					"<strong>" +
					(isStart
						? artRouteData.i18n.startPoint || "Startpunt"
						: artRouteData.i18n.endPoint || "Eindpunt") +
					"</strong>";
				if (notes && notes.trim().length > 0) {
					popupHtml +=
						'<div style="margin-top:4px;max-width:220px;word-break:break-word;">' +
						notes.replace(/\n/g, "<br>") +
						"</div>";
				}
				popupHtml += "</div></div>";
				marker.bindPopup(popupHtml, {
					className: "route-point-popup-container",
				});
				marker.on("click", () => {
					marker.openPopup();
				});
			}

			// Show direction arrows for any point that has arrow_direction set
			if (
				arrowDirection !== null &&
				arrowDirection !== undefined &&
				arrowDirection !== ""
			) {
				const direction = parseFloat(arrowDirection);
				if (!isNaN(direction)) {
					// Create arrow marker
					const arrowHtml = `<div style="transform: rotate(${direction}deg); width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-bottom: 20px solid #ff6b35;"></div>`;
					const arrowMarker = L.marker([lat, lng], {
						icon: L.divIcon({
							className: "route-direction-arrow",
							html: arrowHtml,
							iconSize: [12, 18],
							iconAnchor: [6, 9],
						}),
						zIndexOffset: markerDisplayOrder.DIRECTION_ARROW,
					}).addTo(map);

					// Add popup for direction info
					arrowMarker.bindPopup(
						`<div class="route-point-popup-container"><div class="route-point-popup-content"><strong>Direction Arrow</strong><br>Pointing ${direction}°</div></div>`,
					);
				}
			}
		});
	}

	/**
	 * Generic function to add markers to the map
	 * @param {Array} items - Array of data objects (artworks or info points)
	 * @param {String} type - 'artwork' or 'info-point'
	 * @param {Array} markerArray - Array to store marker references and data
	 * @param {Object} iconOptions - Options for creating the L.divIcon
	 * @param {String} iconOptions.className - Base CSS class for the icon
	 * @param {Function} iconOptions.htmlFn - Function(item, index) returning icon's inner HTML
	 * @param {Array} iconOptions.iconSize - [width, height]
	 * @param {Array} iconOptions.iconAnchor - [x, y]
	 * @param {Object} popupOptions - Options for creating popup (e.g. readMore)
	 */
	function addMapMarkers(items, type, markerArray, iconOptions, popupOptions) {
		if (!items || items.length === 0) {
			return;
		}

		items.forEach((item, index) => {
			// Create custom marker icon
			const iconHtml = iconOptions.htmlFn(item, index);
			const markerIcon = L.divIcon({
				className: iconOptions.className,
				html: iconHtml,
				iconSize: iconOptions.iconSize,
				iconAnchor: iconOptions.iconAnchor,
			});

			// Set zIndexOffset based on type
			let zIndexOffset = markerDisplayOrder.INFO_POINT;
			if (type === "artwork") zIndexOffset = markerDisplayOrder.ARTWORK;
			if (type === "info-point") zIndexOffset = markerDisplayOrder.INFO_POINT;

			// Create the marker
			const marker = L.marker([item.latitude, item.longitude], {
				icon: markerIcon,
				zIndexOffset: zIndexOffset,
			}).addTo(map);

			// Generate the popup content
			const popupContent = createPopupHtml(item, type, popupOptions);

			// Create a permanent popup
			const popup = L.popup({
				maxWidth: 300,
				className: `${type}-popup-container`, // e.g., 'artwork-popup-container'
				closeButton: true,
				autoClose: false,
				closeOnEscapeKey: true,
			}).setContent(popupContent);

			// Add click event to show the popup
			marker.on("click", () => {
				popup.setLatLng(marker.getLatLng()).openOn(map);
			});

			// Store marker info
			const markerData = {
				marker: marker,
				popup: popup,
			};
			markerData[type] = item; // Store item data under its type name (e.g., markerData.artwork = item)

			// Add visited flag specifically for artworks for proximity checking
			if (type === "artwork") {
				markerData.visited = false;
			}

			markerArray.push(markerData);
		});
	}

	/**
	 * Add artwork markers to the map using the generic function
	 */
	function addArtworkMarkers() {
		const artworks = artRouteData.artworks;
		const iconOptions = {
			className: "artwork-marker",
			htmlFn: (artwork) => {
				// Use artwork number if available, otherwise fall back to index + 1
				const displayNumber =
					artwork.number && artwork.number.trim() !== "" ? artwork.number : '';

				// Check if artwork has a custom icon
				if (artwork.icon_url && artwork.icon_url.trim() !== "") {
					return `
						<div class="artwork-marker-inner">
							<div class="artwork-marker-icon" style="background-image: url('${artwork.icon_url}'); background-size: contain; background-repeat: no-repeat; background-position: center; width: 100%; height: 100%; border-radius: 50%;"></div>
						</div>
					`;
				} else {
					// Use default artwork marker with image and number
					return `
						<div class="artwork-marker-inner">
							<div class="artwork-marker-image" style="background-image: url('${artwork.image_url || artRouteData.plugin_url + "assets/images/placeholder.png"}');"></div>
							<div class="artwork-marker-overlay"></div>
							<div class="artwork-marker-number">${displayNumber}</div>
						</div>
					`;
				}
			},
			iconSize: [40, 40],
			iconAnchor: [20, 20],
		};

		addMapMarkers(artworks, "artwork", artworkMarkers, iconOptions);
	}

	/**
	 * Add information point markers to the map using the generic function
	 */
	function addInfoPointMarkers() {
		const infoPoints = artRouteData.information_points;
		const iconOptions = {
			className: "info-point-marker",
			htmlFn: (infoPoint) => {
				if (infoPoint.icon_url) {
					return `<div class="info-point-marker-inner" style="background: none; position: relative;">
                        <div style="width: 100%; height: 100%; background: url('${infoPoint.icon_url}') center center / cover no-repeat;"></div>
                    </div>`;
				} else {
					return '<div class="info-point-marker-inner">i</div>';
				}
			},
			iconSize: [30, 30],
			iconAnchor: [15, 15],
		};

		addMapMarkers(infoPoints, "info-point", infoPointMarkers, iconOptions, {
			readMore: true, // Always show the read more link if a permalink exists
		});
	}

	/**
	 * Create popup HTML content for an artwork or information point
	 * @param {Object} item - The artwork or info point data object
	 * @param {String} type - 'artwork' or 'infoPoint'
	 * @param {Object} options - Additional options for customization (e.g., readMore)
	 * @returns {String} HTML content for the popup
	 */
	function createPopupHtml(item, type, options = { readMore: true }) {
		const { readMore = true } = options;
		const imageUrl = item.image_url; // Get the image URL, might be null/undefined
		const title = item.title || "";
		let content = "";

		if (item.excerpt) {
			content = item.excerpt.trim();
		} else if (item.description) {
			const maxWords = 30;
			const words = item.description.trim().split(/\s+/);
			content = words.length > maxWords ? words.slice(0, maxWords).join(' ') + '...' : item.description.trim();
		}

		const permalink = item.permalink || "";
		const readMoreText = artRouteData.i18n.readMore || "Lees meer"; // Use translated string or default

		// Build image HTML only if imageUrl exists
		let imageHtml = "";
		if (imageUrl) {
			imageHtml = `
                <div class="${type}-popup-image">
                    <img src="${imageUrl}" alt="${title}">
                </div>
            `;
		}

		// Build artists HTML only for artworks
		let artistsHtml = "";
		if (type === "artwork" && item.artists && item.artists.length > 0) {
			artistsHtml = '<div class="artwork-artists">';
			artistsHtml += `<h4>${item.artists.length > 1 ? artRouteData.i18n.artists || "Kunstenaars" : artRouteData.i18n.artist || "Kunstenaar"}:</h4>`;
			artistsHtml += "<ul>";
			item.artists.forEach((artist) => {
				artistsHtml += `<li><a href="${artist.url}" target="_blank">${artist.title}</a></li>`;
			});
			artistsHtml += "</ul></div>";
		}

		// Build accessibility icons HTML for artworks
		let accessibilityHtml = "";
		if (type === "artwork") {
			const wheelchair = item.wheelchair_accessible === true || item.wheelchair_accessible === "1";
			const stroller = item.stroller_accessible === true || item.stroller_accessible === "1";
			if (wheelchair || stroller) {
				accessibilityHtml = '<div class="artwork-accessibility" style="margin: 12px 0 0 0; display: flex; gap: 18px; align-items: center;">';
				if (wheelchair) {
					accessibilityHtml += `<span class=\"artwork-accessibility-item\" title=\"${artRouteData.i18n.wheelchairAccessible || 'Wheelchair accessible'}\"><img src=\"${artRouteData.plugin_url}assets/icons/WB%20plattegrond-Rolstoel.svg\" alt=\"${artRouteData.i18n.wheelchairAccessible || 'Wheelchair accessible'}\" style=\"height:28px;width:28px;\" /></span>`;
				}
				if (stroller) {
					accessibilityHtml += `<span class=\"artwork-accessibility-item\" title=\"${artRouteData.i18n.strollerAccessible || 'Stroller accessible'}\"><img src=\"${artRouteData.plugin_url}assets/icons/WB%20plattegrond-Kinderwagen.svg\" alt=\"${artRouteData.i18n.strollerAccessible || 'Stroller accessible'}\" style=\"height:28px;width:28px;\" /></span>`;
				}
				accessibilityHtml += '</div>';
			}
		}

		// Only show read more for info points if excerpt ends with '...'
		const showReadMore =
			type === "info-point" && content.trim().endsWith("...") && permalink && readMore;

		return `
            <div class="${type}-popup">
                ${imageHtml}
                <div class="${type}-popup-content">
                    <h3>${title}</h3>
                    <div class="${type === "artwork" ? "artwork-description" : "info-point-excerpt"}">
                        ${content}
                    </div>
                    ${accessibilityHtml}
                    ${artistsHtml}
                    ${showReadMore || (type === "artwork" && permalink && readMore) ? `<a href="${permalink}" target="_blank" class="${type}-link">${readMoreText}</a>` : ""}
                </div>
            </div>
        `;
	}

	/**
	 * Show artwork details as a toast (used for proximity detection)
	 */
	function showArtworkDetails(artwork) {
		// Skip showing toasts if disabled
		if (!showArtworkToasts) {
			return;
		}

		// Build artists HTML if there are any
		let artistsHtml = "";
		if (artwork.artists && artwork.artists.length > 0) {
			artistsHtml =
				'<div style="margin-top: 8px; border-top: 1px solid #eee; padding-top: 8px;">';
			artistsHtml += `<strong>${artwork.artists.length > 1 ? artRouteData.i18n.artists || "Kunstenaars" : artRouteData.i18n.artist || "Kunstenaar"}:</strong>`;
			artistsHtml +=
				'<ul style="margin: 5px 0 0 0; padding-left: 20px; font-size: 13px;">';

			artwork.artists.forEach((artist) => {
				artistsHtml += `<li><a href="${artist.url}" target="_blank" style="color: #0073aa; text-decoration: none;">${artist.title}</a>`;
				if (artist.post_type_label) {
					artistsHtml += ` <span style="color: #666; font-style: italic; font-size: 12px;">(${artist.post_type_label})</span>`;
				}
				artistsHtml += "</li>";
			});

			artistsHtml += "</ul></div>";
		}

		// Create a toast instead of showing modal
		const toast = $(`
            <div class="art-route-toast" style="
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                margin-bottom: 10px;
                max-width: 320px;
                overflow: hidden;
                transform: translateX(400px);
                transition: transform 0.3s ease;">
                <div style="position: relative;">
                    <img src="${artwork.image_url}" alt="${artwork.title}" style="width: 100%; height: 160px; object-fit: cover;">
                    <div style="position: absolute; top: 10px; right: 10px; cursor: pointer; background-color: rgba(255,255,255,0.7); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" class="toast-close">
                        &times;
                    </div>
                </div>
                <div style="padding: 16px;">
                    <h3 style="margin: 0 0 8px; font-size: 18px;">${artwork.title}</h3>
                    <div style="font-size: 14px; max-height: 100px; overflow-y: auto; margin-bottom: 8px;">
                        ${artwork.description}
                    </div>
                    ${artistsHtml}
                </div>
            </div>
        `);

		// Add to queue
		toastQueue.push(toast);

		// If no toast is displaying, show this one
		if (!toastDisplaying) {
			showNextToast();
		}
	}

	/**
	 * Display the next toast in queue
	 */
	function showNextToast() {
		if (toastQueue.length === 0) {
			toastDisplaying = false;
			return;
		}

		toastDisplaying = true;
		const toast = toastQueue.shift();

		// Add to container
		$("#art-route-toast-container").append(toast);

		// Animate in
		setTimeout(() => {
			toast.css("transform", "translateX(0)");
		}, 50);

		// Add close handler
		toast.find(".toast-close").on("click", () => {
			hideToast(toast);
		});

		// Auto dismiss after 12 seconds
		setTimeout(() => {
			hideToast(toast);
		}, 12000);
	}

	/**
	 * Hide a toast element
	 */
	function hideToast(toast) {
		toast.css("transform", "translateX(400px)");

		setTimeout(() => {
			toast.remove();
			showNextToast();
		}, 300);
	}

	/**
	 * Update the completed route based on user position
	 */
	function updateCompletedRoute() {
		// Only proceed if showing completed route is enabled
		if (!showCompletedRoute) {
			return;
		}

		if (!userPosition || !routePath || routePath.length === 0) {
			return;
		}

		// Find the closest point on the route
		const closestPoint = findClosestPointOnRoute(userPosition, routePath);
		const closestIndex = closestPoint.index;

		// Update completed path (all points up to the closest one)
		completedPath = routePath.slice(0, closestIndex + 1);

		// Update the completed route layer
		completedRouteLayer.setLatLngs(completedPath);

		// Create a separate line from closest point to user position
		userToRouteLayer.setLatLngs([routePath[closestIndex], userPosition]);

		// Calculate and update progress
		const totalDistance = calculateRouteDistance(routePath);
		const completedDistance = calculateRouteDistance(completedPath);
		let progressPercent = Math.min(
			Math.round((completedDistance / totalDistance) * 100),
			100,
		);

		// If progressPercent is not a number, set to 0 and hide progress UI
		if (isNaN(progressPercent)) {
			progressPercent = 0;
			$(".route-progress").hide();
			return;
		}

		// Update progress UI only if showing completed route is enabled
		$(".route-progress").show();
		$(".progress-fill").css("width", `${progressPercent}%`);
		$("#progress-percentage").text(progressPercent);

		// Check if route is complete
		if (progressPercent >= 98) {
			alert(artRouteData.i18n.routeComplete);
		}
	}

	/**
	 * Find the closest point on the route to the user's position
	 */
	function findClosestPointOnRoute(position, route) {
		let closestIndex = 0;
		let closestDistance = Infinity;

		for (let i = 0; i < route.length; i++) {
			const distance = calculateDistance(
				position[0],
				position[1],
				route[i][0],
				route[i][1],
			);

			if (distance < closestDistance) {
				closestDistance = distance;
				closestIndex = i;
			}
		}

		return {
			point: route[closestIndex],
			index: closestIndex,
			distance: closestDistance,
		};
	}

	/**
	 * Calculate distance between two points (Haversine formula)
	 */
	function calculateDistance(lat1, lon1, lat2, lon2) {
		const R = 6371e3; // Earth radius in meters
		const φ1 = (lat1 * Math.PI) / 180;
		const φ2 = (lat2 * Math.PI) / 180;
		const Δφ = ((lat2 - lat1) * Math.PI) / 180;
		// Note: The order of subtraction (lon1-lon2) is intentional and mathematically equivalent
		// since the result is squared in the haversine formula, so the sign doesn't affect the final distance
		const Δλ = ((lon1 - lon2) * Math.PI) / 180;
		const a =
			Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
			Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
		const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
		return R * c; // in meters
	}

	/**
	 * Calculate total distance of a route
	 */
	function calculateRouteDistance(route) {
		let totalDistance = 0;

		for (let i = 0; i < route.length - 1; i++) {
			totalDistance += calculateDistance(
				route[i][0],
				route[i][1],
				route[i + 1][0],
				route[i + 1][1],
			);
		}

		return totalDistance;
	}

	/**
	 * Check if user is close to any artwork markers
	 */
	function checkArtworkProximity() {
		if (!userPosition) return;

		const proximityThreshold = 50; // meters

		artworkMarkers.forEach((item) => {
			if (item.visited) return;

			const markerPosition = item.marker.getLatLng();
			const distance = calculateDistance(
				userPosition[0],
				userPosition[1],
				markerPosition.lat,
				markerPosition.lng,
			);

			if (distance <= proximityThreshold) {
				// Mark as visited
				item.visited = true;

				// Update marker style
				const icon = item.marker.options.icon;
				const newIconHtml = icon.options.html.replace(
					"artwork-marker-inner",
					"artwork-marker-inner visited",
				);

				const newIcon = L.divIcon({
					className: "artwork-marker",
					html: newIconHtml,
					iconSize: icon.options.iconSize,
					iconAnchor: icon.options.iconAnchor,
				});

				item.marker.setIcon(newIcon);

				// Show artwork details as toast for proximity notifications
				if (showArtworkToasts) {
					showArtworkDetails(item.artwork);
				}

				// Save
				saveVisitedArtwork(item.artwork.id);
			}
		});
	}

	/**
	 * Save visited artwork to server
	 */
	function saveVisitedArtwork(artworkId) {
		$.ajax({
			url: artRouteData.ajax_url,
			type: "POST",
			data: {
				action: "wp_art_routes_mark_artwork_visited",
				artwork_id: artworkId,
				nonce: artRouteData.nonce,
			},
			success: (response) => {
				console.log("Artwork visit saved");
			},
		});
	}

	/**
	 * Clean up resources on page leave
	 */
	$(window).on("beforeunload", () => {
		if (watchId !== null) {
			navigator.geolocation.clearWatch(watchId);
		}
	});

	/**
	 * Initialize map controls for toggling visibility
	 */
	function initMapControls() {
		// Toggle artworks visibility
		$("#toggle-artworks").on("change", function () {
			visibilityState.artworks = this.checked;
			toggleArtworkVisibility(this.checked);
			updateControlItemState(this);
		});

		// Toggle info points visibility
		$("#toggle-info-points").on("change", function () {
			visibilityState.infoPoints = this.checked;
			toggleInfoPointVisibility(this.checked);
			updateControlItemState(this);
		});

		// Toggle route visibility
		$("#toggle-route").on("change", function () {
			visibilityState.route = this.checked;
			toggleRouteVisibility(this.checked);
			updateControlItemState(this);
		});

		// Toggle user location visibility
		$("#toggle-user-location").on("change", function () {
			visibilityState.userLocation = this.checked;
			toggleUserLocationVisibility(this.checked);
			updateControlItemState(this);
		});

		// Navigation buttons
		$("#go-to-my-location").on("click", () => {
			goToUserLocation();
		});

		$("#go-to-route").on("click", () => {
			goToRoute();
		});
	}

	/**
	 * Navigate to user's current location
	 */
	function goToUserLocation() {
		const button = $("#go-to-my-location");

		if (userPosition) {
			// User location is already available, just center on it
			map.setView(userPosition, 18);
		} else {
			// Need to get user location first
			button
				.prop("disabled", true)
				.find(".map-control-label")
				.text(artRouteData.i18n.gettingLocation || "Getting location...");

			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(
					(position) => {
						const lat = position.coords.latitude;
						const lng = position.coords.longitude;

						// Center map on user location
						map.setView([lat, lng], 18);

						// Reset button
						button
							.prop("disabled", false)
							.find(".map-control-label")
							.text(artRouteData.i18n.goToMyLocation || "Go to My Location");
					},
					(error) => {
						// Handle error
						button
							.prop("disabled", false)
							.find(".map-control-label")
							.text(artRouteData.i18n.goToMyLocation || "Go to My Location");

						let errorMessage =
							artRouteData.i18n.locationError || "Could not get your location";
						switch (error.code) {
							case error.PERMISSION_DENIED:
								errorMessage =
									artRouteData.i18n.locationPermissionDenied ||
									"Location access denied. Please allow location access in your browser.";
								break;
							case error.POSITION_UNAVAILABLE:
								errorMessage =
									artRouteData.i18n.locationUnavailable ||
									"Location information is unavailable.";
								break;
							case error.TIMEOUT:
								errorMessage =
									artRouteData.i18n.locationTimeout ||
									"Location request timed out.";
								break;
						}

						alert(errorMessage);
					},
					{
						enableHighAccuracy: true,
						timeout: 10000,
						maximumAge: 60000,
					},
				);
			} else {
				// Geolocation not supported
				button
					.prop("disabled", false)
					.find(".map-control-label")
					.text(artRouteData.i18n.goToMyLocation || "Go to My Location");
				alert(
					artRouteData.i18n.geolocationNotSupported ||
					"Geolocation is not supported by this browser.",
				);
			}
		}
	}

	/**
	 * Navigate to route bounds
	 */
	function goToRoute() {
		if (
			routeLayer &&
			routeLayer.getBounds &&
			routeLayer.getBounds().isValid()
		) {
			// Fit the map to the route bounds with animation
			map.flyToBounds(routeLayer.getBounds(), {
				padding: [50, 50],
				duration: 0.8,
				maxZoom: 16,
			});
		} else if (routePath && routePath.length > 0) {
			// Fallback: create bounds from route path points
			const bounds = L.latLngBounds();
			routePath.forEach((point) => {
				if (Array.isArray(point)) {
					bounds.extend([point[0], point[1]]);
				} else if (point.lat && point.lng) {
					bounds.extend([point.lat, point.lng]);
				}
			});

			if (bounds.isValid()) {
				map.flyToBounds(bounds, {
					padding: [50, 50],
					duration: 0.8,
					maxZoom: 16,
				});
			}
		}
	}

	// Add these functions to support map display toggles
	function toggleArtworkVisibility(show) {
		artworkMarkers.forEach(item => {
			if (show) {
				if (!map.hasLayer(item.marker)) map.addLayer(item.marker);
			} else {
				if (map.hasLayer(item.marker)) map.removeLayer(item.marker);
			}
		});
	}

	function toggleInfoPointVisibility(show) {
		infoPointMarkers.forEach(item => {
			if (show) {
				if (!map.hasLayer(item.marker)) map.addLayer(item.marker);
			} else {
				if (map.hasLayer(item.marker)) map.removeLayer(item.marker);
			}
		});
	}

	function toggleRouteVisibility(show) {
		if (routeLayer) {
			if (show) {
				if (!map.hasLayer(routeLayer)) map.addLayer(routeLayer);
				if (showCompletedRoute && completedRouteLayer && !map.hasLayer(completedRouteLayer)) map.addLayer(completedRouteLayer);
				if (showCompletedRoute && userToRouteLayer && !map.hasLayer(userToRouteLayer)) map.addLayer(userToRouteLayer);
			} else {
				if (map.hasLayer(routeLayer)) map.removeLayer(routeLayer);
				if (completedRouteLayer && map.hasLayer(completedRouteLayer)) map.removeLayer(completedRouteLayer);
				if (userToRouteLayer && map.hasLayer(userToRouteLayer)) map.removeLayer(userToRouteLayer);
			}
		}
	}

	function toggleUserLocationVisibility(show) {
		if (userMarker) {
			if (show) {
				if (!map.hasLayer(userMarker)) map.addLayer(userMarker);
			} else {
				if (map.hasLayer(userMarker)) map.removeLayer(userMarker);
			}
		}
	}
})(jQuery);
