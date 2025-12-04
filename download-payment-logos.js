// Script to download payment logos
// Run: node download-payment-logos.js

const https = require('https');
const fs = require('fs');
const path = require('path');

const logos = [
  {
    url: 'https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-VNPAY-QR.png',
    filename: 'vnpay.png'
  },
  {
    url: 'https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-MoMo-Square.png',
    filename: 'momo.png'
  },
  {
    url: 'https://cdn-icons-png.flaticon.com/512/2830/2830284.png',
    filename: 'bank.png'
  },
  {
    url: 'https://cdn-icons-png.flaticon.com/512/3135/3135706.png',
    filename: 'cash.png'
  }
];

const outputDir = path.join(__dirname, 'public', 'images');

// Create directory if not exists
if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

console.log('📥 Downloading payment logos...\n');

logos.forEach(({ url, filename }) => {
  const outputPath = path.join(outputDir, filename);
  
  https.get(url, (response) => {
    const fileStream = fs.createWriteStream(outputPath);
    response.pipe(fileStream);
    
    fileStream.on('finish', () => {
      fileStream.close();
      console.log(`✅ Downloaded: ${filename}`);
    });
  }).on('error', (err) => {
    console.error(`❌ Error downloading ${filename}:`, err.message);
  });
});

console.log('\n🎉 Download complete! Logos saved to public/images/');
