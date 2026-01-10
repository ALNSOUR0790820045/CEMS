# Contributing to CEMS

We love your input! We want to make contributing to CEMS as easy and transparent as possible, whether it's:

- Reporting a bug
- Discussing the current state of the code
- Submitting a fix
- Proposing new features
- Becoming a maintainer

## Development Process

We use GitHub to host code, track issues and feature requests, as well as accept pull requests.

### Pull Requests

1. Fork the repo and create your branch from `main`.
2. If you've added code that should be tested, add tests.
3. If you've changed APIs, update the documentation.
4. Ensure the test suite passes.
5. Make sure your code lints.
6. Issue that pull request!

### Detailed Steps

1. **Fork the repository**
   ```bash
   # Click the "Fork" button on GitHub
   ```

2. **Clone your fork**
   ```bash
   git clone https://github.com/your-username/CEMS.git
   cd CEMS
   ```

3. **Create a feature branch**
   ```bash
   git checkout -b feature/AmazingFeature
   ```

4. **Make your changes**
   - Write clean, maintainable code
   - Follow the coding standards
   - Add tests for new functionality
   - Update documentation as needed

5. **Commit your changes**
   ```bash
   git add .
   git commit -m 'Add some AmazingFeature'
   ```

6. **Push to your fork**
   ```bash
   git push origin feature/AmazingFeature
   ```

7. **Open a Pull Request**
   - Go to the original repository on GitHub
   - Click "New Pull Request"
   - Select your fork and branch
   - Fill in the PR template with details

## Coding Standards

### PHP Code Style

We follow PSR-12 coding standard. Use Laravel Pint to format your code:

```bash
./vendor/bin/pint
```

### Laravel Best Practices

- Use dependency injection
- Follow SOLID principles
- Keep controllers thin
- Use service classes for business logic
- Use form requests for validation
- Write expressive, readable code

### Naming Conventions

- **Classes**: PascalCase (e.g., `UserController`, `ProjectService`)
- **Methods**: camelCase (e.g., `getUserData()`, `calculateTotal()`)
- **Variables**: camelCase (e.g., `$userId`, `$projectData`)
- **Constants**: UPPER_SNAKE_CASE (e.g., `MAX_ATTEMPTS`)
- **Database tables**: snake_case, plural (e.g., `purchase_orders`)
- **Database columns**: snake_case (e.g., `created_at`, `user_id`)

### Code Documentation

```php
/**
 * Calculate the total amount including tax.
 *
 * @param float $netAmount The net amount before tax
 * @param float $taxRate The tax rate as a percentage
 * @return float The total amount including tax
 */
public function calculateTotal(float $netAmount, float $taxRate): float
{
    return $netAmount * (1 + $taxRate / 100);
}
```

## Testing

All new features must include tests. We use PHPUnit for testing.

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ProjectTest.php

# Run tests with coverage
php artisan test --coverage
```

### Writing Tests

```php
public function test_user_can_create_project(): void
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->postJson('/api/projects', [
            'name' => 'Test Project',
            'code' => 'PROJ-001',
        ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('projects', [
        'code' => 'PROJ-001',
    ]);
}
```

## Git Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests liberally after the first line

### Commit Message Format

```
<type>: <subject>

<body>

<footer>
```

**Types:**
- `feat`: A new feature
- `fix`: A bug fix
- `docs`: Documentation only changes
- `style`: Formatting, missing semicolons, etc.
- `refactor`: Code change that neither fixes a bug nor adds a feature
- `test`: Adding missing tests
- `chore`: Maintenance tasks

**Examples:**

```
feat: add change order approval workflow

- Add multi-level approval process
- Send email notifications to approvers
- Track approval history

Closes #123
```

```
fix: prevent duplicate invoice numbers

Fixed a race condition that allowed duplicate invoice numbers
to be generated when multiple users created invoices simultaneously.

Fixes #456
```

## Code Review Process

All submissions require review. We use GitHub pull requests for this purpose.

### Review Checklist

- [ ] Code follows project conventions
- [ ] Tests pass locally
- [ ] New tests added for new functionality
- [ ] Documentation updated
- [ ] No console errors or warnings
- [ ] Code is self-documenting or has comments where necessary

## Issue Reporting

### Bug Reports

When filing a bug report, please include:

1. **Summary**: A clear and concise description
2. **Steps to reproduce**: Detailed steps to reproduce the behavior
3. **Expected behavior**: What you expected to happen
4. **Actual behavior**: What actually happened
5. **Environment**: 
   - PHP version
   - Laravel version
   - Browser (if frontend issue)
   - Operating System
6. **Screenshots**: If applicable
7. **Additional context**: Any other relevant information

### Feature Requests

When proposing a new feature, please include:

1. **Summary**: Clear description of the feature
2. **Motivation**: Why is this feature needed?
3. **Use cases**: How will it be used?
4. **Proposed implementation**: Your ideas on how to implement it
5. **Alternatives**: Alternative solutions you've considered

## Community Guidelines

### Code of Conduct

- Be respectful and constructive
- Welcome newcomers and help them learn
- Focus on what is best for the community
- Show empathy towards others
- Accept constructive criticism gracefully

### Getting Help

- Check existing documentation first
- Search existing issues before creating new ones
- Join our discussions for questions
- Be patient and respectful when asking for help

## Development Setup

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Configure database**
   ```bash
   # Update .env with your database credentials
   php artisan migrate --seed
   ```

4. **Run development server**
   ```bash
   php artisan serve
   ```

5. **Watch assets**
   ```bash
   npm run dev
   ```

## Project Structure

```
CEMS/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Request handlers
│   │   ├── Middleware/     # HTTP middleware
│   │   └── Requests/       # Form request validation
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic
│   └── Providers/          # Service providers
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/           # Database seeders
│   └── factories/         # Model factories
├── resources/
│   ├── views/             # Blade templates
│   └── js/                # JavaScript files
├── routes/                # Route definitions
├── tests/
│   ├── Feature/           # Feature tests
│   └── Unit/              # Unit tests
└── docs/                  # Documentation
```

## Security Vulnerabilities

If you discover a security vulnerability, please send an email to security@example.com. All security vulnerabilities will be promptly addressed.

**Do not** create public GitHub issues for security vulnerabilities.

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

## Recognition

Contributors will be recognized in:
- README.md contributors section
- Release notes when applicable
- GitHub contributors page

## Questions?

Feel free to open an issue with the `question` label or reach out to the maintainers.

---

Thank you for contributing to CEMS! 🎉
