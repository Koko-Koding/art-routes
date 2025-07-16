function dmsToDecimal(
	degrees: number,
	minutes: number,
	seconds: number,
	direction: string,
): number {
	let decimal = degrees + minutes / 60 + seconds / 3600;
	if (direction === "S" || direction === "W") {
		decimal *= -1;
	}
	return decimal;
}

function parseCoordinates(input: string): {
	latitude: number;
	longitude: number;
} {
	const regex = /(\d+)°(\d+)'([\d.]+)"?([NS])\s+(\d+)°(\d+)'([\d.]+)"?([EW])/;
	const match = input.match(regex);

	if (!match) {
		throw new Error("Invalid coordinate format");
	}

	const [, latDeg, latMin, latSec, latDir, lonDeg, lonMin, lonSec, lonDir] =
		match;

	const latitude = dmsToDecimal(
		parseInt(latDeg),
		parseInt(latMin),
		parseFloat(latSec),
		latDir,
	);

	const longitude = dmsToDecimal(
		parseInt(lonDeg),
		parseInt(lonMin),
		parseFloat(lonSec),
		lonDir,
	);

	return { latitude, longitude };
}

// Example usage:
const input = process.argv[2]; // e.g. '52°04\'37.8"N 5°38\'59.2"E'
if (!input) {
	console.error('Usage: bun convert-gps.ts "52°04\'37.8\\"N 5°38\'59.2\\"E"');
	process.exit(1);
}

try {
	const { latitude, longitude } = parseCoordinates(input);
	console.log(`Latitude: ${latitude}`);
	console.log(`Longitude: ${longitude}`);
} catch (err) {
	console.error("Error parsing coordinates:", err);
}
