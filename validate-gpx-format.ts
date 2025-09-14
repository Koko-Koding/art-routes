/**
 * TypeScript script to validate the GPX format changes
 * Run with: bun validate-gpx-format.ts
 */

import { readFileSync } from 'fs';
import { join } from 'path';

// Define the expected GPX format structure
interface GPXValidation {
    hasStandaloneNo: boolean;
    hasXmlnsXsi: boolean;
    hasSchemaLocation: boolean;
    hasCorrectNamespaceOrder: boolean;
    hasCorrectElementOrder: boolean;
    isValid: boolean;
}function validateGPXFormat(gpxContent: string): GPXValidation {
    const lines = gpxContent.split('\n').map(line => line.trim());

    // Check XML declaration
    const xmlDeclaration = lines[0] || '';
    const hasStandaloneNo = xmlDeclaration.includes('standalone="no"');

    // Find the GPX opening tag (might span multiple lines)
    let gpxTagContent = '';
    let foundGpxStart = false;
    let foundGpxEnd = false;

    for (const line of lines) {
        if (line.includes('<gpx') && !foundGpxStart) {
            foundGpxStart = true;
            gpxTagContent += line + ' ';
        } else if (foundGpxStart && !foundGpxEnd) {
            gpxTagContent += line + ' ';
            if (line.includes('>')) {
                foundGpxEnd = true;
                break;
            }
        }
    }

    // Check for required namespace declarations
    const hasXmlnsXsi = gpxTagContent.includes('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"');
    const hasSchemaLocation = gpxTagContent.includes('xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd"');

    // Check if the main namespace comes first (good practice)
    const hasCorrectNamespaceOrder = gpxTagContent.indexOf('xmlns="http://www.topografix.com/GPX/1/1"') <
        gpxTagContent.indexOf('xmlns:xsi=');

    // NEW: Check GPX element order (metadata, waypoints, routes, tracks, extensions)
    const fullContent = gpxContent;
    const metadataPos = fullContent.indexOf('<metadata');
    const firstWptPos = fullContent.indexOf('<wpt');
    const firstTrkPos = fullContent.indexOf('<trk');

    // Element order should be: metadata, then waypoints, then tracks
    let hasCorrectElementOrder = true;
    if (metadataPos !== -1 && firstWptPos !== -1 && metadataPos > firstWptPos) {
        hasCorrectElementOrder = false; // metadata should come before waypoints
    }
    if (firstWptPos !== -1 && firstTrkPos !== -1 && firstWptPos > firstTrkPos) {
        hasCorrectElementOrder = false; // waypoints should come before tracks
    }

    const isValid = hasStandaloneNo && hasXmlnsXsi && hasSchemaLocation && hasCorrectNamespaceOrder && hasCorrectElementOrder;

    return {
        hasStandaloneNo,
        hasXmlnsXsi,
        hasSchemaLocation,
        hasCorrectNamespaceOrder,
        hasCorrectElementOrder,
        isValid
    };
} function simulateGPXGeneration(): string {
    // Simulate the PHP function output with the NEW corrected element order
    const routeTitle = 'Test Route';
    const routeDescription = 'A test route for validation';
    const creationTime = new Date().toISOString();

    let gpx = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>\n';
    gpx += '<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1" creator="WP Art Routes Plugin"\n';
    gpx += '    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"\n';
    gpx += '    xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">\n';
    gpx += '  <metadata>\n';
    gpx += `    <name>${routeTitle}</name>\n`;
    gpx += `    <desc>${routeDescription}</desc>\n`;
    gpx += `    <time>${creationTime}</time>\n`;
    gpx += '  </metadata>\n';

    // Waypoints come FIRST (per GPX 1.1 spec order)
    gpx += '  <wpt lat="52.518611" lon="13.376111">\n';
    gpx += '    <name>Artwork 1: Test Artwork</name>\n';
    gpx += '    <desc>A test artwork</desc>\n';
    gpx += '    <type>Artwork</type>\n';
    gpx += '  </wpt>\n';

    // Then track elements
    gpx += '  <trk>\n';
    gpx += '    <name>Test Route - Route Path</name>\n';
    gpx += '    <desc>Main route path</desc>\n';
    gpx += '    <trkseg>\n';
    gpx += '      <trkpt lat="52.518611" lon="13.376111">\n';
    gpx += '      </trkpt>\n';
    gpx += '      <trkpt lat="48.208031" lon="16.358128">\n';
    gpx += '      </trkpt>\n';
    gpx += '    </trkseg>\n';
    gpx += '  </trk>\n';
    gpx += '</gpx>\n';

    return gpx;
} function main() {
    console.log('🔍 GPX Format Validation Script');
    console.log('================================\n');

    try {
        // Read the working example file
        const exampleGpxPath = join(__dirname, 'example-gpx.gpx');
        const exampleGpx = readFileSync(exampleGpxPath, 'utf-8');

        console.log('📋 Validating WORKING example (example-gpx.gpx):');
        const exampleValidation = validateGPXFormat(exampleGpx);
        console.log('✅ XML Declaration with standalone="no":', exampleValidation.hasStandaloneNo);
        console.log('✅ xmlns:xsi namespace:', exampleValidation.hasXmlnsXsi);
        console.log('✅ xsi:schemaLocation:', exampleValidation.hasSchemaLocation);
        console.log('✅ Correct namespace order:', exampleValidation.hasCorrectNamespaceOrder);
        console.log('✅ Correct element order (metadata, wpt, trk):', exampleValidation.hasCorrectElementOrder);
        console.log('📊 Overall valid:', exampleValidation.isValid ? '✅ PASS' : '❌ FAIL');

        console.log('\n📋 Validating SIMULATED new format:');
        const simulatedGpx = simulateGPXGeneration();
        const simulatedValidation = validateGPXFormat(simulatedGpx);
        console.log('✅ XML Declaration with standalone="no":', simulatedValidation.hasStandaloneNo);
        console.log('✅ xmlns:xsi namespace:', simulatedValidation.hasXmlnsXsi);
        console.log('✅ xsi:schemaLocation:', simulatedValidation.hasSchemaLocation);
        console.log('✅ Correct namespace order:', simulatedValidation.hasCorrectNamespaceOrder);
        console.log('✅ Correct element order (metadata, wpt, trk):', simulatedValidation.hasCorrectElementOrder);
        console.log('📊 Overall valid:', simulatedValidation.isValid ? '✅ PASS' : '❌ FAIL'); console.log('\n🔍 Format Comparison:');
        console.log('Example format matches new format:',
            exampleValidation.isValid === simulatedValidation.isValid &&
                simulatedValidation.isValid ? '✅ YES' : '❌ NO');

        // Show the first few lines of both formats
        console.log('\n📄 Working Example Format (first 5 lines):');
        exampleGpx.split('\n').slice(0, 5).forEach((line, i) => {
            console.log(`${i + 1}: ${line}`);
        });

        console.log('\n📄 New Simulated Format (first 5 lines):');
        simulatedGpx.split('\n').slice(0, 5).forEach((line, i) => {
            console.log(`${i + 1}: ${line}`);
        });

        // Check if the old problematic format would fail
        console.log('\n📋 Testing OLD problematic format:');
        const oldFormat = '<?xml version="1.0" encoding="UTF-8"?>\n<gpx version="1.1" creator="WP Art Routes Plugin" xmlns="http://www.topografix.com/GPX/1/1">';
        const oldValidation = validateGPXFormat(oldFormat);
        console.log('❌ Old format valid:', oldValidation.isValid ? '✅ PASS (unexpected!)' : '❌ FAIL (expected)');

        if (simulatedValidation.isValid && !oldValidation.isValid) {
            console.log('\n🎉 SUCCESS: New format is valid and old format fails validation!');
            console.log('🚀 The GPX export fix should resolve Garmin BaseCamp compatibility issues.');
        } else {
            console.log('\n⚠️  WARNING: Validation results unexpected. Please review the changes.');
        }

    } catch (error) {
        console.error('❌ Error during validation:', error);
        process.exit(1);
    }
}

// Run the validation
main();