import vue from 'eslint-plugin-vue'
import tseslint from 'typescript-eslint'

export default [
    ...vue.configs['flat/recommended'],
    ...tseslint.configs.recommended,
    {
        files: ['**/*.{ts,tsx,vue,js}'],
        rules: {
            'vue/multi-word-component-names': 'off',
        },
    },
]
