# Dockerfile for WordPress plugin development with gettext, Composer, and code quality tools
FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && \
    apt-get install -y \
        gettext \
        git \
        unzip \
        wget \
        curl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Configure Composer to allow plugins globally
RUN composer global config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true

# Install PHP_CodeSniffer and WordPress Coding Standards with all dependencies
RUN composer global require "squizlabs/php_codesniffer:^3.7" && \
    composer global require "wp-coding-standards/wpcs:^3.0" && \
    composer global require "dealerdirect/phpcodesniffer-composer-installer:^1.0" && \
    composer global require "phpcsstandards/phpcsutils:^1.0" && \
    composer global require "phpcsstandards/phpcsextra:^1.0" && \
    composer global require "phpcompatibility/phpcompatibility-wp:^2.1"

# Add composer global bin to PATH
ENV PATH="/root/.composer/vendor/bin:${PATH}"

# Configure PHPCS with WordPress standards
RUN phpcs --config-set installed_paths /root/.composer/vendor/wp-coding-standards/wpcs,/root/.composer/vendor/phpcompatibility/phpcompatibility-wp,/root/.composer/vendor/phpcsstandards/phpcsutils,/root/.composer/vendor/phpcsstandards/phpcsextra

# Create useful aliases and scripts
RUN echo '#!/bin/bash\n\
# WordPress Code Quality Scripts\n\
\n\
case "$1" in\n\
    "check")\n\
        echo "Running PHPCS check..."\n\
        if [ -f "phpcs.xml" ]; then\n\
            phpcs\n\
        else\n\
            phpcs --standard=WordPress --extensions=php --ignore=*/vendor/*,*/node_modules/*,*/build/* .\n\
        fi\n\
        ;;\n\
    "fix")\n\
        echo "Running PHPCBF auto-fix..."\n\
        if [ -f "phpcs.xml" ]; then\n\
            phpcbf\n\
        else\n\
            phpcbf --standard=WordPress --extensions=php --ignore=*/vendor/*,*/node_modules/*,*/build/* .\n\
        fi\n\
        ;;\n\
    "check-security")\n\
        echo "Running security-focused checks..."\n\
        phpcs --standard=WordPress --sniffs=WordPress.Security --extensions=php --ignore=*/vendor/*,*/node_modules/*,*/build/* .\n\
        ;;\n\
    "check-i18n")\n\
        echo "Running internationalization checks..."\n\
        phpcs --standard=WordPress --sniffs=WordPress.WP.I18n --extensions=php --ignore=*/vendor/*,*/node_modules/*,*/build/* .\n\
        ;;\n\
    "compile-po")\n\
        echo "Compiling .po files to .mo..."\n\
        if [ -d "languages" ]; then\n\
            find languages -name "*.po" -exec sh -c '\''msgfmt "$1" -o "${1%.po}.mo"'\'' _ {} \;\n\
            echo "Translation files compiled successfully."\n\
        else\n\
            echo "No languages directory found."\n\
        fi\n\
        ;;\n\
    "help"|*)\n\
        echo "WordPress Plugin Development Tools"\n\
        echo "Usage: wp-tools [command]"\n\
        echo ""\n\
        echo "Commands:"\n\
        echo "  check         - Run PHPCS check with WordPress standards"\n\
        echo "  fix           - Auto-fix code style issues with PHPCBF"\n\
        echo "  check-security - Check for security issues only"\n\
        echo "  check-i18n    - Check internationalization compliance"\n\
        echo "  compile-po    - Compile all .po files to .mo"\n\
        echo "  help          - Show this help message"\n\
        echo ""\n\
        echo "Examples:"\n\
        echo "  docker run --rm -v \$PWD:/app wp-dev-tools check"\n\
        echo "  docker run --rm -v \$PWD:/app wp-dev-tools fix"\n\
        echo "  docker run --rm -v \$PWD:/app wp-dev-tools compile-po"\n\
        ;;\n\
esac' > /usr/local/bin/wp-tools && \
    chmod +x /usr/local/bin/wp-tools

# Set default command
CMD ["wp-tools", "help"]

# Usage examples:
# Build: docker build -t wp-dev-tools .
# Check code: docker run --rm -v ${PWD}:/app wp-dev-tools check
# Fix code: docker run --rm -v ${PWD}:/app wp-dev-tools fix
# Security check: docker run --rm -v ${PWD}:/app wp-dev-tools check-security
# i18n check: docker run --rm -v ${PWD}:/app wp-dev-tools check-i18n
# Compile translations: docker run --rm -v ${PWD}:/app wp-dev-tools compile-po
