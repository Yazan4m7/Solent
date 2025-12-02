function normalize(str) {
    return str.trim().toLowerCase().replace(/\s+/g, '');
}

function levenshtein(a, b) {
    const dp = Array(a.length + 1).fill().map(() => Array(b.length + 1).fill(0));
    for (let i = 0; i <= a.length; i++) dp[i][0] = i;
    for (let j = 0; j <= b.length; j++) dp[0][j] = j;

    for (let i = 1; i <= a.length; i++) {
        for (let j = 1; j <= b.length; j++) {
            const cost = a[i - 1] === b[j - 1] ? 0 : 1;
            dp[i][j] = Math.min(
                dp[i - 1][j] + 1,
                dp[i][j - 1] + 1,
                dp[i - 1][j - 1] + cost
            );
        }
    }

    return dp[a.length][b.length];
}

// STRICT fuzzy matcher: only allow 1 char difference (no partials!)
function fuzzyMatch(a, b) {
    a = normalize(a);
    b = normalize(b);

    if (a === b) return true;

    if (Math.abs(a.length - b.length) > 1) return false;

    let mismatch = 0;
    let i = 0, j = 0;

    while (i < a.length && j < b.length) {
        if (a[i] !== b[j]) {
            mismatch++;
            if (mismatch > 1) return false;

            if (a.length > b.length) i++;
            else if (b.length > a.length) j++;
            else {
                i++;
                j++;
            }
        } else {
            i++;
            j++;
        }
    }

    mismatch += (a.length - i) + (b.length - j);

    return mismatch <= 1;
}

// 🔧 Keep same names as you’re using now:
document.getByFuzzyId = function (inputId) {
    const all = document.querySelectorAll('[id]');
    for (const el of all) {
        if (fuzzyMatch(inputId, el.id)) return el;
    }
    return null;
};

document.getByFuzzyClass = function (inputClass) {
    const all = document.querySelectorAll('[class]');
    for (const el of all) {
        if (fuzzyMatch(inputClass, el.className)) return el;
    }
    return null;
};
