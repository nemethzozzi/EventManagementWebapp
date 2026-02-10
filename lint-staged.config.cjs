module.exports = {
    // FRONTEND: ESLint + Prettier
    'frontend/**/*.{js,jsx,ts,tsx,vue}': [
        'npx --prefix frontend eslint --fix',
        'prettier --write',
    ],

    // BACKEND: Laravel Pint (formatter)
    'backend/**/*.php': [
        'php backend/vendor/bin/pint',
    ],
}
