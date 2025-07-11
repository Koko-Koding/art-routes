const fs = require('fs');
const path = require('path');
const axios = require('axios');
const csv = require('csv-parser');
require('dotenv').config();

const {
  WP_USER,
  WP_APP_PASSWORD,
  WP_SITE,
} = process.env;

const AUTH_HEADER = {
  Authorization: `Basic ${Buffer.from(`${WP_USER}:${WP_APP_PASSWORD}`).toString('base64')}`,
};

const capitalize = (text) =>
  text.replace(/\b\w/g, (char) => char.toUpperCase());

const INPUT_CSV = path.join(__dirname, 'data.csv');

fs.createReadStream(INPUT_CSV)
  .pipe(csv({ separator: '\t' })) // Use '\t' for tab-separated files
  .on('data', async (row) => {
    const name = row['name'];
    const latitude = row['latitude'];
    const longitude = row['longitude'];
    const rawAddress = row['full_address'];
    const address = capitalize(rawAddress || '');

    try {
      const res = await axios.post(
        `${WP_SITE}/wp-json/wp/v2/information_point`,
        {
          title: name,
          content: address,
          status: 'publish',
          meta: {
            _artwork_latitude: latitude,
            _artwork_longitude: longitude,
            // _info_point_icon_url: 'optional_url_here'
          },
        },
        { headers: AUTH_HEADER }
      );
      console.log(`✅ Created: ${name}`);
    } catch (err) {
      console.error(`❌ Error creating "${name}":`, err.response?.data || err.message);
    }
  })
  .on('end', () => {
    console.log('🎉 All done!');
  });
