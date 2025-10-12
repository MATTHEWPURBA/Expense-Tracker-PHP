# 🌍 AI Multi-Language & Multi-Currency Support

## ✅ Now Supporting Multiple Languages!

Your AI-powered expense tracker now understands **ANY language** and automatically converts amounts based on your currency!

---

## 🎯 Supported Languages

The AI can understand:

- 🇬🇧 **English** - "I spent $50 on pizza"
- 🇮🇩 **Indonesian (Bahasa Indonesia)** - "beli pizza 50 ribu"
- 🇨🇳 **Chinese (中文)** - "買了100元的咖啡"
- 🇪🇸 **Spanish** - "gasté 50 euros en comida"
- 🇫🇷 **French** - "j'ai dépensé 50 euros"
- 🇩🇪 **German** - "50 Euro für Essen ausgegeben"
- And many more!

---

## 💰 Currency Support

### Set Your Currency First:
1. Go to **Settings** page
2. Select your currency (USD, IDR, EUR, GBP, etc.)
3. Save changes

The AI will automatically understand your currency!

---

## 🇮🇩 Indonesian Examples

### Basic Format:
```
"beli [item] [amount] [currency]"
```

### Amount Recognition:

| Indonesian Term | Number Value | Example |
|----------------|--------------|---------|
| "50 ribu" | 50,000 | "beli kopi 50 ribu" |
| "1 juta" | 1,000,000 | "bayar listrik 1 juta" |
| "500k" | 500,000 | "belanja 500k" |
| "2.5 juta" | 2,500,000 | "gaji bonus 2.5 juta" |
| "35 ribu rupiah" | 35,000 | "uber 35 ribu rupiah" |

### Real Examples:

#### Food & Dining:
```
✅ "beli pizza buat temen kantor 500 ribu rupiah"
   → Amount: 500,000 IDR
   → Description: "pizza buat temen kantor"
   → Category: food

✅ "makan siang di restoran 75 ribu"
   → Amount: 75,000 IDR
   → Description: "makan siang di restoran"
   → Category: food

✅ "beli kopi starbucks 50k"
   → Amount: 50,000 IDR
   → Description: "kopi starbucks"
   → Category: food
```

#### Transportation:
```
✅ "uber ke kantor 35 ribu"
   → Amount: 35,000 IDR
   → Description: "uber ke kantor"
   → Category: transport

✅ "gojek pulang 25k"
   → Amount: 25,000 IDR
   → Description: "gojek pulang"
   → Category: transport

✅ "bensin motor 50 ribu"
   → Amount: 50,000 IDR
   → Description: "bensin motor"
   → Category: transport
```

#### Utilities:
```
✅ "bayar listrik 1 juta"
   → Amount: 1,000,000 IDR
   → Description: "bayar listrik"
   → Category: utilities

✅ "internet wifi 350 ribu"
   → Amount: 350,000 IDR
   → Description: "internet wifi"
   → Category: utilities
```

#### Shopping:
```
✅ "belanja baju di mall 500k"
   → Amount: 500,000 IDR
   → Description: "belanja baju di mall"
   → Category: shopping

✅ "beli sepatu 750 ribu"
   → Amount: 750,000 IDR
   → Description: "beli sepatu"
   → Category: shopping
```

#### Entertainment:
```
✅ "nonton bioskop 50 ribu"
   → Amount: 50,000 IDR
   → Description: "nonton bioskop"
   → Category: entertainment

✅ "netflix subscription 150k"
   → Amount: 150,000 IDR
   → Description: "netflix subscription"
   → Category: entertainment
```

---

## 🇬🇧 English Examples

### Format:
```
"[action] [amount] [currency] on/for [item]"
```

### Examples:

```
✅ "I spent $50 on pizza"
   → Amount: 50
   → Description: "pizza"
   → Category: food

✅ "Paid 25 dollars for Uber to airport"
   → Amount: 25
   → Description: "Uber to airport"
   → Category: transport

✅ "$15.99 for Netflix subscription"
   → Amount: 15.99
   → Description: "Netflix subscription"
   → Category: entertainment

✅ "100 bucks for groceries at Walmart"
   → Amount: 100
   → Description: "groceries at Walmart"
   → Category: food
```

---

## 🔢 Amount Format Recognition

### The AI understands ALL these formats:

#### Exact Numbers:
- `50` → 50
- `50000` → 50,000
- `1000000` → 1,000,000

#### With Thousand Markers:
- `50k` → 50,000
- `50 ribu` → 50,000
- `50 thousand` → 50,000
- `50K` → 50,000 (case insensitive)

#### With Million Markers:
- `1m` → 1,000,000
- `1 juta` → 1,000,000
- `1 million` → 1,000,000
- `2.5 juta` → 2,500,000

#### Decimals:
- `50.5` → 50.5
- `50,5` → 50.5 (European format)
- `1.5 juta` → 1,500,000
- `2.75 million` → 2,750,000

---

## 🎯 Tips for Best Results

### ✅ DO:
1. Include the amount clearly
2. Mention what you bought/paid for
3. Use natural language (like you're texting a friend)
4. Mix languages if needed! (e.g., "beli netflix subscription 150k")

### ❌ DON'T:
1. Use overly complex sentences
2. Forget to mention the amount
3. Use abbreviations AI might not understand

---

## 🌟 Mixed Language Examples

The AI is smart enough to understand mixed languages:

```
✅ "beli netflix subscription 150 ribu"
   (Indonesian + English)

✅ "uber to kantor 35k"
   (English + Indonesian)

✅ "bayar bills 500 ribu"
   (Indonesian + English)
```

---

## 💡 Common Patterns

### Indonesian Patterns:
- `beli [item] [amount]` - "bought [item] for [amount]"
- `bayar [item] [amount]` - "paid [item] for [amount]"
- `belanja [item] [amount]` - "shopping [item] for [amount]"

### English Patterns:
- `I spent [amount] on [item]`
- `Paid [amount] for [item]`
- `[amount] for [item]`
- `Bought [item] for [amount]`

---

## 🔧 Troubleshooting

### "Could not parse expense"

**Reasons:**
1. Amount is missing or unclear
2. Text is too vague

**Solutions:**
1. Make sure to include a clear amount
2. Simplify your text
3. Use examples from this guide

### Examples of What Might Fail:

❌ "beli pizza" (no amount)
✅ "beli pizza 50 ribu" (has amount)

❌ "spent some money yesterday" (no specific amount)
✅ "spent 50k yesterday on food" (specific amount)

---

## 📊 Category Detection

The AI automatically detects categories based on keywords:

### Food & Dining:
- Keywords: pizza, kopi, makan, restaurant, food, coffee, lunch, dinner, groceries

### Transportation:
- Keywords: uber, gojek, taxi, bensin, gas, parking, bus, transport

### Utilities:
- Keywords: listrik, electricity, water, internet, wifi, phone bill

### Entertainment:
- Keywords: bioskop, netflix, movie, game, cinema, subscription

### Healthcare:
- Keywords: doctor, medicine, gym, hospital, obat

### Shopping:
- Keywords: belanja, baju, sepatu, clothes, shoes, mall, shopping

### Other:
- Everything else

---

## 🎉 Try It Now!

### Step 1: Set Your Currency
Go to Settings → Select **Indonesian Rupiah (IDR)** → Save

### Step 2: Go to Dashboard
Scroll down to **🤖 AI-Powered Features**

### Step 3: Type in the Input
Example: `beli pizza buat temen kantor 500 ribu rupiah`

### Step 4: Click "Parse & Add Expense"
Watch the magic happen! ✨

### Step 5: Review & Submit
The form will auto-fill:
- Amount: 500,000
- Description: "pizza buat temen kantor"
- Category: Food & Dining

Click "Add Expense" to save!

---

## 🌐 More Languages Coming Soon!

Want to add support for your language? The AI already understands most languages, but we can improve the prompts!

Just let us know which language you'd like better support for.

---

## 📝 Summary

✅ Supports **ANY language**
✅ Understands **Indonesian** perfectly (ribu, juta, k, m)
✅ Recognizes **YOUR currency** automatically
✅ Smart **amount parsing** (50k, 1 juta, etc.)
✅ Automatic **category detection**
✅ Natural language processing
✅ **100% FREE** using Google Gemini

---

**Start tracking expenses in your language today!** 🎊

Your expense tracker is now truly international! 🌍

