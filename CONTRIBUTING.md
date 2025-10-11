# 🤝 Contributing to Expense Tracker

First off, thank you for considering contributing to Expense Tracker! It's people like you that make this project better for everyone.

---

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)

---

## 📜 Code of Conduct

### Our Pledge

We pledge to make participation in our project a harassment-free experience for everyone, regardless of:
- Age
- Body size
- Disability
- Ethnicity
- Gender identity
- Level of experience
- Nationality
- Personal appearance
- Race
- Religion
- Sexual identity and orientation

### Our Standards

**Positive behavior includes:**
- Using welcoming and inclusive language
- Being respectful of differing viewpoints
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

**Unacceptable behavior includes:**
- Trolling, insulting/derogatory comments, and personal attacks
- Public or private harassment
- Publishing others' private information without permission
- Other conduct which could reasonably be considered inappropriate

---

## 🎯 How Can I Contribute?

### Reporting Bugs

**Before submitting a bug report:**
- Check the [existing issues](https://github.com/yourusername/expense-tracker-php/issues)
- Try to reproduce the issue on the latest version
- Collect information about the bug

**Bug Report Template:**

```markdown
**Description:**
A clear description of the bug.

**Steps to Reproduce:**
1. Go to '...'
2. Click on '...'
3. Scroll down to '...'
4. See error

**Expected Behavior:**
What you expected to happen.

**Actual Behavior:**
What actually happened.

**Screenshots:**
If applicable, add screenshots.

**Environment:**
- PHP Version: [e.g. 7.4]
- Browser: [e.g. Chrome 90]
- Hosting: [e.g. InfinityFree]
- OS: [e.g. Windows 10]

**Additional Context:**
Any other relevant information.
```

### Suggesting Enhancements

**Before submitting an enhancement:**
- Check if the feature already exists
- Search for similar suggestions in issues
- Consider if it fits the project's scope

**Enhancement Request Template:**

```markdown
**Feature Description:**
Clear description of the enhancement.

**Motivation:**
Why would this be useful?

**Proposed Solution:**
How you envision this working.

**Alternatives Considered:**
Other solutions you've thought about.

**Additional Context:**
Screenshots, mockups, or examples.
```

### Your First Code Contribution

**Good first issues:**
- Documentation improvements
- Adding comments to code
- Fixing typos
- Small bug fixes
- UI/UX improvements

Look for issues labeled:
- `good first issue`
- `beginner friendly`
- `documentation`
- `help wanted`

---

## 🔧 Development Setup

### 1. Fork the Repository

```bash
# Click "Fork" button on GitHub
# Then clone your fork:
git clone https://github.com/YOUR-USERNAME/expense-tracker-php.git
cd expense-tracker-php
```

### 2. Create a Branch

```bash
# Create a new branch for your feature/fix
git checkout -b feature/your-feature-name

# Or for bug fixes:
git checkout -b fix/bug-description
```

### 3. Set Up Development Environment

**Option A: PHP Built-in Server**
```bash
php -S localhost:8000
```

**Option B: XAMPP/WAMP**
- Copy project to `htdocs/`
- Start Apache
- Visit `http://localhost/expense-tracker/`

### 4. Make Your Changes

- Write clean, readable code
- Follow existing code style
- Add comments where necessary
- Test your changes thoroughly

### 5. Test Locally

**Manual Testing:**
```
✅ Add expenses
✅ Delete expenses
✅ Export CSV
✅ Chart displays
✅ Mobile responsive
✅ No console errors
```

**Test Different Scenarios:**
```
- Empty data
- Large data (100+ expenses)
- All categories used
- Edge cases (very large amounts, long descriptions)
```

---

## 📝 Coding Standards

### PHP Style Guide

```php
<?php
// Use clear, descriptive names
function calculateMonthlyTotal($expenses) {
    // Always add comments for complex logic
    $total = 0;
    
    // Use meaningful variable names
    foreach ($expenses as $expense) {
        if ($this->isCurrentMonth($expense['date'])) {
            $total += $expense['amount'];
        }
    }
    
    return $total;
}

// Always sanitize user input
$description = htmlspecialchars($_POST['description']);

// Use type hints when possible (PHP 7.0+)
function addExpense(array $expense): bool {
    // Implementation
}
```

### JavaScript Style Guide

```javascript
// Use const/let, not var
const expenses = [];
let total = 0;

// Use async/await for promises
async function addExpense(formData) {
    try {
        const response = await fetch('', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// Use meaningful function names
function calculateCategoryTotals(expenses) {
    // Clear logic with comments
    return expenses.reduce((totals, expense) => {
        totals[expense.category] = (totals[expense.category] || 0) + expense.amount;
        return totals;
    }, {});
}
```

### CSS Style Guide

```css
/* Use clear class names */
.expense-item {
    /* Group related properties */
    display: flex;
    align-items: center;
    gap: 10px;
    
    /* Add comments for complex styles */
    padding: 15px;
    border-radius: 10px;
    
    /* Use CSS variables for colors */
    background-color: var(--light);
    border: 2px solid var(--border);
}

/* Mobile-first approach */
@media (max-width: 768px) {
    .expense-item {
        flex-direction: column;
    }
}
```

---

## 💬 Commit Guidelines

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style changes (formatting, etc.)
- **refactor**: Code refactoring
- **test**: Adding tests
- **chore**: Maintenance tasks

### Examples

```bash
# Good commits
git commit -m "feat(export): add PDF export functionality"
git commit -m "fix(chart): resolve chart rendering issue on mobile"
git commit -m "docs(readme): update installation instructions"
git commit -m "style(css): improve expense list spacing"

# Bad commits (avoid these)
git commit -m "fixed stuff"
git commit -m "changes"
git commit -m "asdfasdf"
```

### Detailed Commit Example

```
feat(categories): add ability to create custom categories

- Add CRUD operations for categories
- Add color picker for custom colors
- Add emoji selector for icons
- Update UI to show custom categories
- Add migration for existing data

Closes #42
```

---

## 🔄 Pull Request Process

### Before Submitting

1. **Update your fork:**
   ```bash
   git fetch upstream
   git merge upstream/main
   ```

2. **Test thoroughly:**
   - All features work
   - No console errors
   - No PHP errors
   - Mobile responsive

3. **Update documentation:**
   - README if needed
   - Code comments
   - CHANGELOG if exists

4. **Clean up commits:**
   ```bash
   # Squash multiple commits if needed
   git rebase -i HEAD~3
   ```

### Submitting Pull Request

1. **Push to your fork:**
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Create PR on GitHub:**
   - Go to original repository
   - Click "New Pull Request"
   - Select your branch
   - Fill in the PR template

### PR Template

```markdown
## Description
Brief description of changes.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Changes Made
- Added feature X
- Fixed bug Y
- Updated documentation Z

## Testing Done
- [ ] Tested locally
- [ ] Tested on mobile
- [ ] Tested with large datasets
- [ ] No console errors

## Screenshots (if applicable)
[Add screenshots here]

## Checklist
- [ ] Code follows style guidelines
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No breaking changes (or documented)
- [ ] Tested thoroughly

## Related Issues
Closes #123
```

### After Submission

- Respond to review comments
- Make requested changes
- Update PR if needed
- Be patient and respectful

---

## 🎨 Feature Contribution Ideas

### Easy (Good for beginners)

- [ ] Add more category icons/colors
- [ ] Improve error messages
- [ ] Add loading animations
- [ ] Enhance mobile UI
- [ ] Add keyboard shortcuts
- [ ] Improve accessibility (ARIA labels)
- [ ] Add more export formats (JSON, XML)
- [ ] Create dark mode toggle

### Medium

- [ ] Add search/filter functionality
- [ ] Implement pagination
- [ ] Add recurring expenses
- [ ] Create budget limits
- [ ] Add income tracking
- [ ] Multi-currency support
- [ ] Monthly/yearly reports
- [ ] Advanced analytics

### Advanced

- [ ] Multi-user authentication
- [ ] Database migration option
- [ ] REST API
- [ ] Mobile app (PWA)
- [ ] Bank integration
- [ ] Receipt scanning
- [ ] AI spending insights
- [ ] Email notifications

---

## 🧪 Testing Guidelines

### Manual Testing

1. **Fresh Installation:**
   ```bash
   rm -rf data/
   # Test first-time setup
   ```

2. **Edge Cases:**
   - Empty data
   - Very large amounts ($999,999.99)
   - Long descriptions (1000+ characters)
   - Past dates (years ago)
   - Future dates

3. **Browser Testing:**
   - Chrome
   - Firefox
   - Safari
   - Edge
   - Mobile browsers

### Automated Testing (Future)

```php
// Example PHPUnit test (not yet implemented)
class ExpenseTest extends PHPUnit\Framework\TestCase {
    public function testAddExpense() {
        // Test implementation
    }
}
```

---

## 📚 Resources

### Learn More

- [PHP Documentation](https://www.php.net/docs.php)
- [Chart.js Documentation](https://www.chartjs.org/docs/)
- [MDN Web Docs](https://developer.mozilla.org/)
- [Git Handbook](https://guides.github.com/introduction/git-handbook/)

### Inspiration

- Look at similar projects
- Check GitHub trending repositories
- Browse design inspiration sites

---

## ❓ Questions?

### Where to Ask

- **GitHub Issues:** For bugs and features
- **GitHub Discussions:** For general questions
- **Email:** your.email@example.com

### Response Time

- Bug reports: 1-3 days
- Feature requests: 1-7 days
- Pull requests: 1-5 days

---

## 🎉 Recognition

Contributors will be:
- Listed in README
- Credited in CHANGELOG
- Mentioned in release notes
- Given eternal gratitude! 🙏

---

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

<div align="center">

**Thank you for contributing! 🚀**

Every contribution, no matter how small, makes a difference!

[Back to README](README.md) | [View Issues](https://github.com/yourusername/expense-tracker-php/issues) | [Create PR](https://github.com/yourusername/expense-tracker-php/pulls)

</div>


