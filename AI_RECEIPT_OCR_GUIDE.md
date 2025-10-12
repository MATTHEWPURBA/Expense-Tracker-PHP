# 🧾 Receipt OCR - AI Feature Guide

## ✨ NEW FEATURE: Scan Receipt Photos!

Extract expense information automatically from receipt photos using AI vision technology.

---

## 🎯 What It Does

Upload a photo of any receipt and AI will automatically extract:
- 💰 **Total Amount** - The final total on your receipt
- 🏪 **Merchant Name** - Store/restaurant name
- 📅 **Date** - Transaction date (converted to YYYY-MM-DD)
- 📦 **Items** - List of purchased items (up to 10)
- 🏷️ **Category** - Smart categorization (food, transport, etc.)
- ✅ **Confidence Level** - How confident AI is about the extraction

---

## 📱 How to Use

### Step 1: Take a Receipt Photo
- Use your phone camera or scanner
- Make sure the receipt is:
  - **Clear** and in focus
  - **Well-lit** (no shadows)
  - **Flat** (not crumpled)
  - **Complete** (all text visible)

### Step 2: Upload to Dashboard
1. Go to **Dashboard** (index.php)
2. Scroll to **🤖 AI-Powered Features** section
3. Find **🧾 Receipt Scanner (OCR)**
4. Click **📷 Choose Receipt Photo**
5. Select your receipt image
6. Click **🧾 Scan Receipt**

### Step 3: Review & Submit
- AI fills in the expense form automatically
- Review the extracted information
- Make any corrections if needed
- Click **Add Expense** to save

---

## 🖼️ Supported Formats

### Image Types:
- ✅ **JPEG / JPG** - Most common format
- ✅ **PNG** - High quality screenshots
- ✅ **WebP** - Modern web format

### Image Requirements:
- **Max Size:** 10MB
- **Min Quality:** Clear enough to read text
- **Orientation:** Any (AI handles rotation)

---

## 🌍 Language Support

The Receipt OCR supports **ANY language**:

### ✅ Tested Languages:
- 🇺🇸 **English** - "Starbucks Coffee"
- 🇮🇩 **Indonesian** - "Alfamart"
- 🇨🇳 **Chinese** - "星巴克咖啡"
- 🇪🇸 **Spanish** - "Mercado"
- 🇫🇷 **French** - "Carrefour"
- 🇩🇪 **German** - "Supermarkt"
- And many more!

### Currency Recognition:
The AI automatically recognizes:
- **USD** ($, dollar, dollars)
- **IDR** (Rp, rupiah, ribu, juta)
- **EUR** (€, euro, euros)
- **GBP** (£, pound, pounds)
- **And more!**

---

## 📊 What Gets Extracted

### Example Receipt:
```
==================
   STARBUCKS
  Main Street
==================
Latte           $5.50
Croissant       $3.50
-------------------
Subtotal:       $9.00
Tax:            $0.90
-------------------
TOTAL:          $9.90
-------------------
Date: 10/12/2025
==================
```

### AI Extracts:
```json
{
  "amount": 9.90,
  "merchant": "Starbucks",
  "date": "2025-10-12",
  "category": "food",
  "items": ["Latte", "Croissant"],
  "confidence": "high"
}
```

### Form Auto-Fills:
- **Amount:** 9.90
- **Description:** "Starbucks (Latte, Croissant)"
- **Category:** Food & Dining
- **Date:** 2025-10-12

---

## 🎨 Receipt Types Supported

### ✅ Works Great With:
- 🍔 **Restaurant receipts** - Fast food, dining, cafes
- 🛒 **Grocery store receipts** - Supermarkets, convenience stores
- ⛽ **Gas station receipts** - Fuel purchases
- 🛍️ **Retail receipts** - Shopping malls, stores
- 🎫 **Service receipts** - Uber, taxi, parking
- 💊 **Pharmacy receipts** - Medicine, health products
- 🎬 **Entertainment receipts** - Movies, events
- 🔧 **Utility bills** - Electricity, water, internet

### ⚠️ May Have Issues With:
- 📄 Handwritten receipts (hard to read)
- 🌀 Thermal receipts that faded
- 💧 Water-damaged receipts
- 📝 Receipts with missing totals
- 🎨 Complex/artistic receipts

---

## 💡 Tips for Best Results

### ✅ DO:
1. **Take photo in good lighting**
2. **Keep camera steady**
3. **Capture entire receipt**
4. **Use high resolution**
5. **Flatten crumpled receipts**
6. **Clean dirty/stained receipts**

### ❌ DON'T:
1. Use blurry photos
2. Cut off parts of receipt
3. Take photos in shadows
4. Upload receipts with missing info
5. Use extremely low resolution

---

## 🔧 API Endpoint

### POST `/api_ai.php?action=receipt`

**Request:**
```json
{
  "image": "base64_encoded_image_data",
  "mimeType": "image/jpeg"
}
```

**Response (Success):**
```json
{
  "amount": 50.99,
  "merchant": "Store Name",
  "date": "2025-10-12",
  "category": "food",
  "items": ["Item 1", "Item 2"],
  "description": "Store Name (Item 1, Item 2)",
  "confidence": "high",
  "success": true
}
```

**Response (Error):**
```json
{
  "error": "Could not read receipt",
  "success": false
}
```

---

## 🚀 Technical Details

### Backend:
- **Service:** `AIService.php` → `extractExpenseFromReceiptImage()`
- **Controller:** `AIController.php` → `scanReceipt()`
- **API:** `api_ai.php` → action=receipt

### Frontend:
- **JavaScript:** `ai-dashboard.js` → `scanReceipt()`
- **UI:** `views/dashboard/index.php` → Feature 3

### AI Model:
- **Engine:** Google Gemini 2.5 Flash (Vision API)
- **Input:** Image (base64) + Text prompt
- **Output:** Structured JSON with expense data
- **Cost:** FREE (included in free tier)

---

## 📈 Processing Flow

```
1. User selects receipt photo
   ↓
2. JavaScript converts to base64
   ↓
3. POST to /api_ai.php?action=receipt
   ↓
4. AIController validates image
   ↓
5. AIService calls Gemini Vision API
   ↓
6. AI analyzes image and extracts data
   ↓
7. JSON response with expense details
   ↓
8. JavaScript auto-fills form
   ↓
9. User reviews and submits
```

---

## 🔐 Security & Privacy

### ✅ Your Data is Safe:
- **No Storage:** Images are NOT saved on server
- **Temporary:** Processed in memory only
- **Private:** Each user's receipts are isolated
- **Secure:** API requires authentication
- **Encrypted:** All data sent via HTTPS

### Privacy Notes:
- Images sent to Google Gemini API for processing
- Google processes images but doesn't store them
- See Google's privacy policy: https://ai.google.dev/

---

## 💰 Pricing & Limits

### Cost: **$0.00** (FREE!)
- Vision API is included in Gemini free tier
- No additional cost for image processing
- Same rate limits as text API

### Rate Limits:
- **Free Tier:** 60 requests/minute, 1,500/day
- **Typical Usage:** ~3-5 receipt scans/day
- **Well Within Limits:** Only 0.2-0.3% of daily quota

### File Size Limits:
- **Max Image Size:** 10MB
- **Recommended:** Under 2MB for faster processing
- **Typical Receipt Photo:** 500KB - 2MB

---

## 🐛 Troubleshooting

### "Image data required"
**Problem:** No image selected  
**Solution:** Click "Choose Receipt Photo" and select an image

### "Unsupported image format"
**Problem:** Wrong file type  
**Solution:** Use JPEG, PNG, or WebP only

### "Image too large"
**Problem:** File exceeds 10MB  
**Solution:** Compress image or use lower resolution

### "Could not read receipt"
**Problem:** Receipt is unclear or damaged  
**Solutions:**
- Retake photo with better lighting
- Flatten crumpled receipt
- Ensure entire receipt is visible
- Try a different angle

### "Invalid image data"
**Problem:** Corrupted file or upload error  
**Solution:** Try uploading again or use different image

### Processing takes too long
**Problem:** Large image or slow connection  
**Solutions:**
- Use smaller image (compress)
- Check internet connection
- Try again during off-peak hours

---

## 📚 Code Examples

### JavaScript - Scan Receipt:
```javascript
async function scanReceipt() {
    const fileInput = document.getElementById('receipt-file-input');
    const file = fileInput.files[0];
    
    // Convert to base64
    const base64Image = await fileToBase64(file);
    
    // Call API
    const result = await aiRequest('receipt', { 
        image: base64Image,
        mimeType: file.type 
    }, 'POST');
    
    if (result.success) {
        // Fill form with extracted data
        document.getElementById('expense-amount').value = result.amount;
        document.getElementById('expense-description').value = result.description;
        document.getElementById('expense-category').value = result.category;
    }
}
```

### PHP - Process Receipt:
```php
// AIController.php
public function scanReceipt(): void {
    $input = json_decode(file_get_contents('php://input'), true);
    $imageData = $input['image'];
    $mimeType = $input['mimeType'] ?? 'image/jpeg';
    
    $result = $this->aiService->extractExpenseFromReceiptImage(
        $imageData, 
        $mimeType, 
        $userCurrency
    );
    
    echo json_encode(array_merge($result, ['success' => true]));
}
```

---

## 🎉 Real-World Examples

### Example 1: Indonesian Restaurant
**Photo:** Alfamart receipt  
**Extracted:**
- Amount: 125,000 IDR
- Merchant: "Alfamart"
- Category: Shopping
- Items: ["Mie Goreng", "Air Mineral", "Sabun"]

### Example 2: US Gas Station
**Photo:** Shell gas station  
**Extracted:**
- Amount: 45.50 USD
- Merchant: "Shell"
- Category: Transport
- Items: ["Regular Unleaded - 12.5 gal"]

### Example 3: Chinese Restaurant
**Photo:** Chinese restaurant bill  
**Extracted:**
- Amount: 188 CNY
- Merchant: "海底捞火锅"
- Category: Food
- Items: ["麻辣火锅", "啤酒"]

---

## 🆕 What's New

### Version 1.0 (October 2025)
- ✅ Initial release
- ✅ Multi-language support
- ✅ Multi-currency support
- ✅ Auto-categorization
- ✅ Item extraction
- ✅ Date parsing
- ✅ Confidence scoring

### Coming Soon:
- 📸 Mobile camera integration
- 🔄 Batch upload (multiple receipts)
- 📊 Receipt history/archive
- 🎯 Better accuracy for handwritten receipts
- 🌟 Receipt templates for common stores

---

## 📞 Support

### Need Help?
- **Error Messages:** Check troubleshooting section above
- **Setup Issues:** See main `AI_README.md`
- **API Docs:** See `AI_IMPLEMENTATION_GUIDE.md`
- **Vision API Docs:** https://ai.google.dev/tutorials/vision

### Report Issues:
- If OCR consistently fails for specific receipt types
- If accuracy is poor for your language
- If you encounter bugs or errors

---

## 🎓 How It Works (Technical)

### AI Vision Processing:
1. Image converted to base64 format
2. Sent to Gemini Vision API with structured prompt
3. AI analyzes image using computer vision
4. Extracts text using OCR (Optical Character Recognition)
5. Understands context (what's merchant, what's total, etc.)
6. Structures data into JSON format
7. Returns to application

### Prompt Engineering:
The AI is given specific instructions:
- Find TOTAL amount (not individual items)
- Extract merchant/store name
- Parse date format
- List purchased items
- Categorize based on merchant type
- Handle multiple languages/currencies

---

## 🏆 Benefits

### For Users:
- ⏱️ **Save Time** - No manual entry
- ✅ **Accuracy** - Reduces human error
- 📱 **Convenience** - Just snap & go
- 🌍 **Universal** - Works anywhere
- 💯 **Complete** - Captures all details

### For Business:
- 📊 **Better Data** - Detailed transaction records
- 🔍 **Audit Trail** - Keep digital receipt copies
- 💼 **Expense Reports** - Easy reconciliation
- 📈 **Analytics** - Track spending by merchant

---

## 🎊 Conclusion

The **Receipt OCR** feature transforms expense tracking from a tedious manual process into a quick, effortless scan!

### Get Started:
1. Have your API key configured (`config.php`)
2. Go to dashboard
3. Take a photo of any receipt
4. Click scan
5. Done! ✨

**No more typing receipts manually!** 🎉

---

**Built with ❤️ using Google Gemini Vision AI (FREE tier)**

**Version:** 1.0.0  
**Release Date:** October 2025  
**Status:** Production Ready ✅

