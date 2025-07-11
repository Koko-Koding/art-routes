import fs from 'fs';
import path from 'path';
import axios from 'axios';
import csv from 'csv-parser';
import { distance as levenshteinDistance } from 'fastest-levenshtein';
import stringSimilarity from 'string-similarity';
import 'dotenv/config';

const {
  WP_USER,
  WP_APP_PASSWORD,
  WP_SITE,
} = process.env;

const AUTH_HEADER = {
  Authorization: `Basic ${Buffer.from(`${WP_USER}:${WP_APP_PASSWORD}`).toString('base64')}`,
};

interface InformationPoint {
  id: number;
  title: { rendered: string };
  content: { rendered: string };
  meta: {
    _artwork_latitude?: string;
    _artwork_longitude?: string;
  };
}

interface CSVRow {
  name: string;
  id: string;
  latitude: string;
  longitude: string;
  description: string;
  city: string;
  full_address: string;
}

const capitalize = (text: string): string =>
  text.replace(/\b\w/g, (char) => char.toUpperCase());

// Smart similarity checking function
const calculateSimilarity = (str1: string, str2: string): number => {
  if (!str1 || !str2) return 0;
  
  const normalized1 = str1.toLowerCase().trim();
  const normalized2 = str2.toLowerCase().trim();
  
  // Exact match
  if (normalized1 === normalized2) return 1;
  
  // Calculate Levenshtein distance (normalized)
  const maxLength = Math.max(str1.length, str2.length);
  const levenshteinSimilarity = 1 - (levenshteinDistance(normalized1, normalized2) / maxLength);
  
  // Calculate Jaro-Winkler similarity
  const jaroWinklerSimilarity = stringSimilarity.compareTwoStrings(normalized1, normalized2);
  
  // Return weighted average (give more weight to Jaro-Winkler for fuzzy matching)
  return (levenshteinSimilarity * 0.3) + (jaroWinklerSimilarity * 0.7);
};

// Check if coordinates are similar (within ~100 meters)
const areCoordinatesSimilar = (lat1: string, lon1: string, lat2: string, lon2: string): boolean => {
  const lat1Num = parseFloat(lat1);
  const lon1Num = parseFloat(lon1);
  const lat2Num = parseFloat(lat2);
  const lon2Num = parseFloat(lon2);
  
  if (isNaN(lat1Num) || isNaN(lon1Num) || isNaN(lat2Num) || isNaN(lon2Num)) {
    return false;
  }
  
  // Rough approximation: 0.001 degrees ≈ 100 meters
  const latDiff = Math.abs(lat1Num - lat2Num);
  const lonDiff = Math.abs(lon1Num - lon2Num);
  
  return latDiff < 0.001 && lonDiff < 0.001;
};

// Find similar existing information point
const findSimilarPoint = (csvRow: CSVRow, existingPoints: InformationPoint[]): InformationPoint | null => {
  let bestMatch: InformationPoint | null = null;
  let bestScore = 0;
  const SIMILARITY_THRESHOLD = 0.7; // 70% similarity threshold
  
  for (const point of existingPoints) {
    let score = 0;
    let factors = 0;
    
    // Compare names
    const nameScore = calculateSimilarity(csvRow.name, point.title.rendered);
    if (nameScore > 0.5) { // Only consider if name is somewhat similar
      score += nameScore * 2; // Give name high weight
      factors += 2;
    }
    
    // Compare coordinates if both points have them
    const existingLat = point.meta?._artwork_latitude || '';
    const existingLon = point.meta?._artwork_longitude || '';
    
    if (csvRow.latitude && csvRow.longitude && existingLat && existingLon) {
      if (areCoordinatesSimilar(csvRow.latitude, csvRow.longitude, existingLat, existingLon)) {
        score += 1; // Full score for coordinate match
        factors += 1;
      }
    }
    
    // Compare addresses
    const existingContent = point.content?.rendered || '';
    const addressScore = calculateSimilarity(csvRow.full_address, existingContent);
    if (addressScore > 0.3) {
      score += addressScore;
      factors += 1;
    }
    
    // Calculate average score
    const avgScore = factors > 0 ? score / factors : 0;
    
    if (avgScore > SIMILARITY_THRESHOLD && avgScore > bestScore) {
      bestScore = avgScore;
      bestMatch = point;
    }
  }

  console.log(`🔍 Similarity check for "${csvRow.name}": Best match score = ${bestScore.toFixed(2)}`);
  
  return bestMatch;
};

// Fetch all existing information points
const fetchAllInformationPoints = async (): Promise<InformationPoint[]> => {
  console.log('🔍 Fetching existing information points...');
  
  try {
    const response = await axios.get(
      `${WP_SITE}/wp-json/wp/v2/information_point?per_page=100&_embed&context=edit`,
      { headers: AUTH_HEADER }
    );
    
    console.log(`📍 Found ${response.data.length} existing information points`);
    
    // Debug: Show meta data for first point
    if (response.data.length > 0) {
      console.log(`🔧 Sample point meta:`, response.data[0].meta);
    }
    
    return response.data;
  } catch (error) {
    console.error('❌ Error fetching information points:', error.response?.data || error.message);
    return [];
  }
};

// Create new information point
const createInformationPoint = async (csvRow: CSVRow): Promise<void> => {
  const address = capitalize(csvRow.full_address || '');
  
  const requestData = {
    title: csvRow.name,
    content: address,
    status: 'publish',
    meta: {
      _artwork_latitude: csvRow.latitude,
      _artwork_longitude: csvRow.longitude,
    },
  };
  
  console.log(`🔧 Creating with name, lat & lon: ${requestData.title}, ${requestData.meta._artwork_latitude}, ${requestData.meta._artwork_longitude}`);
  
  try {
    const response = await axios.post(
      `${WP_SITE}/wp-json/wp/v2/information_point`,
      requestData,
      { headers: AUTH_HEADER }
    );
    console.log(`✅ Created: ${csvRow.name}`);
    console.log(`📍 Response meta:`, response.data.meta);
  } catch (error) {
    console.error(`❌ Error creating "${csvRow.name}":`, error.response?.data || error.message);
  }
};

// Update existing information point
const updateInformationPoint = async (existingPoint: InformationPoint, csvRow: CSVRow): Promise<void> => {
  const address = capitalize(csvRow.full_address || '');
  
  const requestData = {
    title: csvRow.name,
    content: address,
    status: 'publish',
    meta: {
      _artwork_latitude: csvRow.latitude,
      _artwork_longitude: csvRow.longitude,
    },
  };
  
  console.log(`🔧 Updating ID ${existingPoint.id} with data:`, JSON.stringify(requestData, null, 2));
  
  try {
    const response = await axios.put(
      `${WP_SITE}/wp-json/wp/v2/information_point/${existingPoint.id}`,
      requestData,
      { headers: AUTH_HEADER }
    );
    console.log(`🔄 Updated: ${csvRow.name} (was: ${existingPoint.title.rendered})`);
    console.log(`📍 Response meta:`, response.data.meta);
  } catch (error) {
    console.error(`❌ Error updating "${csvRow.name}":`, error.response?.data || error.message);
  }
};

// Main processing function
const processCSVData = async (): Promise<void> => {
  const INPUT_CSV = path.join(import.meta.dir, 'data.csv');
  const existingPoints = await fetchAllInformationPoints();
  
  return new Promise((resolve, reject) => {
    const csvRows: CSVRow[] = [];
    
    fs.createReadStream(INPUT_CSV)
      .pipe(csv({ separator: '\t' }))
      .on('data', (row: CSVRow) => {
        csvRows.push(row);
      })
      .on('end', async () => {
        console.log(`📊 Processing ${csvRows.length} rows from CSV...`);
        
        for (const row of csvRows) {
          console.log(`🔍 Checking: ${row.name} | ${row.latitude}, ${row.longitude}`);
          const similarPoint = findSimilarPoint(row, existingPoints);
          
          if (similarPoint) {
            // also log coordinates (first 5 chars)
            console.log(`🔄 Found similar point!: ${similarPoint.title.rendered}`);
            await updateInformationPoint(similarPoint, row);
          } else {
            console.log(`➕ Creating new point: ${row.name}`);
            await createInformationPoint(row);
          }
        }
        
        console.log('🎉 All done!');
        resolve();
      })
      .on('error', reject);
  });
};

// Run the process
processCSVData().catch(console.error);
