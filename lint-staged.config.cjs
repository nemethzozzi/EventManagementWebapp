module.exports = {
    // FRONTEND: ESLint + Prettier
    'frontend/**/*.{js,jsx,ts,tsx,vue}': [
        'bash -lc "cd frontend && npx eslint . --fix"',
        'bash -lc "cd frontend && npx prettier . --write"',
    ],

    // BACKEND: Laravel Pint (formatter)
    'backend/**/*.php': [
        'bash -lc "cd backend && php vendor/bin/pint"',
    ],
}
