import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

/**
 * Make paths work no matter where the script is executed from.
 * Script location: frontend/scripts/checkLangKeys.mjs
 */
const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const FRONTEND_DIR = path.resolve(__dirname, '..')
const ROOT = path.resolve(__dirname, '..', '..')

/**
 * ANSI colors (no deps)
 */
const C = {
    reset: '\x1b[0m',
    red: '\x1b[31m',
    yellow: '\x1b[33m',
    green: '\x1b[32m',
    cyan: '\x1b[36m',
    bold: '\x1b[1m',
}

function red(s) {
    return `${C.red}${s}${C.reset}`
}
function yellow(s) {
    return `${C.yellow}${s}${C.reset}`
}
function green(s) {
    return `${C.green}${s}${C.reset}`
}
function cyan(s) {
    return `${C.cyan}${s}${C.reset}`
}
function bold(s) {
    return `${C.bold}${s}${C.reset}`
}

/**
 * CONFIG
 */
const FRONTEND_LOCALES_DIR = path.join(FRONTEND_DIR, 'src', 'locales')

const SCAN_DIRS = [
    path.join(FRONTEND_DIR, 'src'),
    path.join(ROOT, 'backend', 'app'),
    path.join(ROOT, 'backend', 'resources'),
    path.join(ROOT, 'backend', 'routes'),
]

const ALLOWED_EXT = new Set(['.ts', '.tsx', '.js', '.jsx', '.vue', '.php', '.blade.php'])

const IGNORE_DIR_PARTS = [
    `${path.sep}node_modules${path.sep}`,
    `${path.sep}vendor${path.sep}`,
    `${path.sep}dist${path.sep}`,
    `${path.sep}storage${path.sep}`,
    `${path.sep}bootstrap${path.sep}cache${path.sep}`,
    `${path.sep}.git${path.sep}`,
    `${path.sep}.idea${path.sep}`,
    `${path.sep}.vscode${path.sep}`,
]

/**
 * Regexes to extract used translation keys (string literal only)
 */
const KEY_REGEXES = [
    /\$t\(\s*['"`]([^'"`]+?)['"`]\s*[\),]/g,
    /(^|[^.\w])t\(\s*['"`]([^'"`]+?)['"`]\s*[\),]/g,
    /i18n\.global\.t\(\s*['"`]([^'"`]+?)['"`]\s*[\),]/g,
    /__\(\s*['"`]([^'"`]+?)['"`]\s*[\),]/g,
    /trans\(\s*['"`]([^'"`]+?)['"`]\s*[\),]/g,
    /Lang::get\(\s*['"`]([^'"`]+?)['"`]\s*[\),]/g,
]

function isIgnored(filePath) {
    return IGNORE_DIR_PARTS.some((p) => filePath.includes(p))
}

function walk(dir) {
    const out = []
    if (!fs.existsSync(dir)) return out

    const entries = fs.readdirSync(dir, { withFileTypes: true })
    for (const e of entries) {
        const full = path.join(dir, e.name)
        if (isIgnored(full)) continue

        if (e.isDirectory()) {
            out.push(...walk(full))
        } else {
            const ext = e.name.endsWith('.blade.php') ? '.blade.php' : path.extname(e.name)
            if (ALLOWED_EXT.has(ext)) out.push(full)
        }
    }
    return out
}

function flattenJsonKeys(obj, prefix = '') {
    const keys = []
    if (!obj || typeof obj !== 'object' || Array.isArray(obj)) return keys

    for (const [k, v] of Object.entries(obj)) {
        const next = prefix ? `${prefix}.${k}` : k
        if (v && typeof v === 'object' && !Array.isArray(v)) {
            keys.push(...flattenJsonKeys(v, next))
        } else {
            keys.push(next)
        }
    }

    return keys
}

function loadLocaleKeys() {
    if (!fs.existsSync(FRONTEND_LOCALES_DIR)) {
        throw new Error(`Locales directory not found: ${FRONTEND_LOCALES_DIR}`)
    }

    const files = fs
        .readdirSync(FRONTEND_LOCALES_DIR)
        .filter((f) => f.endsWith('.json'))

    if (!files.length) {
        throw new Error(`No locale json found in: ${FRONTEND_LOCALES_DIR}`)
    }

    const allKeys = new Set()
    const perLocale = {}

    for (const file of files) {
        const full = path.join(FRONTEND_LOCALES_DIR, file)
        const raw = fs.readFileSync(full, 'utf-8')

        let json
        try {
            json = JSON.parse(raw)
        } catch (e) {
            throw new Error(`Invalid JSON in ${full}: ${e.message}`)
        }

        const keys = flattenJsonKeys(json)
        const set = new Set(keys)
        perLocale[file] = set
        keys.forEach((k) => allKeys.add(k))
    }

    return { files, allKeys, perLocale }
}

function extractUsedKeysFromFile(filePath) {
    const text = fs.readFileSync(filePath, 'utf-8')
    const keys = new Set()

    for (const re of KEY_REGEXES) {
        let m
        while ((m = re.exec(text)) !== null) {
            const candidate = m[2] ?? m[1]
            if (!candidate) continue

            const key = String(candidate).trim()
            if (!key) continue
            if (key.includes(' ')) continue
            if (key.startsWith('http')) continue

            keys.add(key)
        }
    }

    return keys
}

function extractUsedKeys() {
    const files = SCAN_DIRS.flatMap(walk)
    const usedKeys = new Set()

    for (const f of files) {
        const ks = extractUsedKeysFromFile(f)
        ks.forEach((k) => usedKeys.add(k))
    }

    return { usedKeys, scannedFilesCount: files.length }
}

function printList(title, items, colorFn, limit = 200) {
    console.log(`\n${bold(title)} ${cyan(`(${items.length})`)}`)
    items.slice(0, limit).forEach((x) => console.log(colorFn(`- ${x}`)))
    if (items.length > limit) console.log(yellow(`... and ${items.length - limit} more`))
}

function main() {
    const { files: localeFiles, allKeys, perLocale } = loadLocaleKeys()
    const { usedKeys, scannedFilesCount } = extractUsedKeys()

    const missing = [...usedKeys].filter((k) => !allKeys.has(k)).sort()
    const unused = [...allKeys].filter((k) => !usedKeys.has(k)).sort()

    const localeMissingMap = {}
    for (const lf of localeFiles) {
        const set = perLocale[lf]
        const missingInLocale = [...allKeys].filter((k) => !set.has(k)).sort()
        localeMissingMap[lf] = missingInLocale
    }

    const hasAnyLocaleGaps = Object.values(localeMissingMap).some((arr) => arr.length > 0)

    console.log(bold('i18n key check'))
    console.log(`- Repo root: ${cyan(ROOT)}`)
    console.log(`- Frontend dir: ${cyan(FRONTEND_DIR)}`)
    console.log(`- Locales dir: ${cyan(FRONTEND_LOCALES_DIR)}`)
    console.log(`- Scanned files: ${cyan(String(scannedFilesCount))}`)
    console.log(`- Locales: ${cyan(localeFiles.join(', '))}`)
    console.log(`- Total locale keys (union): ${cyan(String(allKeys.size))}`)
    console.log(`- Used keys (code): ${cyan(String(usedKeys.size))}`)

    if (missing.length) {
        printList(
            red('USED IN CODE BUT MISSING IN JSON'),
            missing,
            (s) => red(s)
        )
    }

    if (unused.length) {
        printList(
            yellow('PRESENT IN JSON BUT UNUSED IN CODE'),
            unused,
            (s) => yellow(s)
        )
    }

    if (hasAnyLocaleGaps) {
        console.log(`\n${bold(yellow('LOCALE COMPLETENESS (missing keys per locale)'))}`)
        for (const [lf, arr] of Object.entries(localeMissingMap)) {
            if (!arr.length) continue
            console.log(`\n${yellow(lf)} ${cyan(`missing: ${arr.length}`)}`)
            arr.slice(0, 50).forEach((k) => console.log(yellow(`- ${k}`)))
            if (arr.length > 50) console.log(yellow(`... and ${arr.length - 50} more`))
        }
    }

    if (missing.length || unused.length || hasAnyLocaleGaps) {
        console.log('\n' + red('❌ i18n check failed'))
        process.exitCode = 1
    } else {
        console.log('\n' + green('✅ OK: no missing/unused keys detected'))
    }
}

main()
