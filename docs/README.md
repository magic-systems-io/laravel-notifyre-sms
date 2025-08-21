# Notifyre Laravel Package - Documentation

Welcome to the Notifyre Laravel package documentation! This guide will help you get started and master all the features of the package.

## 📚 Documentation Index

### 🚀 Getting Started
- **[Installation Guide](./INSTALLATION.md)** - Complete setup instructions and environment configuration
- **[Configuration Guide](./CONFIGURATION.md)** - All configuration options and environment variables

### 💡 How to Use
- **[Usage Guide](./USAGE.md)** - Detailed usage examples for direct SMS and notifications
- **[Examples](./EXAMPLES.md)** - Real-world examples and best practices for common scenarios

### 🔧 Technical Details
- **[Commands Guide](./COMMANDS.md)** - All Artisan commands and customization
- **[Architecture Guide](./ARCHITECTURE.md)** - Package internals and extension points
- **[Drivers Guide](./DRIVERS.md)** - SMS and Log driver implementation details
- **[Testing Guide](./../tests/README.md)** - Testing strategies and examples

## 🎯 Quick Navigation

### For New Users
1. Start with **[Installation Guide](./INSTALLATION.md)** to get the package running
2. Check **[Usage Guide](./USAGE.md)** for basic examples
3. Review **[Examples](./EXAMPLES.md)** for real-world scenarios

### For Developers
1. **[Configuration Guide](./CONFIGURATION.md)** for all available options
2. **[Commands Guide](./COMMANDS.md)** for command customization and comprehensive testing
3. **[Architecture Guide](./ARCHITECTURE.md)** for package internals and extension
4. **[Drivers Guide](./DRIVERS.md)** for technical implementation details
5. **[Testing Guide](./../tests/README.md)** for testing strategies

### For Production Deployment
1. **[Installation Guide](./INSTALLATION.md)** - Production setup section
2. **[Configuration Guide](./CONFIGURATION.md)** - Production configuration
3. **[Drivers Guide](./DRIVERS.md)** - SMS driver configuration

## 🔍 What You'll Find Here

### Installation & Setup
- Composer installation
- Environment configuration
- Artisan commands for setup
- Minimal vs. full configuration options

### Usage Examples
- Direct SMS sending
- Laravel notifications
- CLI commands
- Error handling
- Rate limiting

### Configuration Options
- Driver selection
- API settings
- Retry logic
- Caching options
- Rate limiting
- Default values

### Advanced Features
- Custom drivers
- Queue integration
- Event handling
- Testing strategies
- Performance optimization

## 🆘 Need Help?

- **Check the examples** - Most common use cases are covered
- **Review configuration** - Ensure your environment is set up correctly
- **Check the tests** - See how the package is intended to be used
- **Open an issue** - If you can't find what you need

## 📖 Package Structure

```
notifyre-laravel/
├── src/                    # Source code
│   ├── Channels/          # Notification channels
│   ├── Commands/          # Artisan commands
│   ├── Contracts/         # Interfaces
│   ├── DTO/              # Data transfer objects
│   ├── Enums/            # Enumerations
│   ├── Facades/          # Laravel facades
│   ├── Providers/        # Service providers
│   └── Services/         # Core services
├── docs/                  # This documentation
├── tests/                 # Test suite
└── config/                # Configuration files
```

---

**Ready to get started?** Begin with the [Installation Guide](./INSTALLATION.md)!
