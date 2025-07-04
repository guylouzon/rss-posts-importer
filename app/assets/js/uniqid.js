/**
 * Generates a unique ID, similar to PHP's uniqid().
 * @param {string} [prefix=''] - Optional prefix for the ID.
 * @param {boolean} [more_entropy=false] - If true, adds additional entropy.
 * @returns {string} The unique ID.
 */
function uniqid(prefix = '', more_entropy = false) {
    let seed = uniqid._seed = (uniqid._seed || Math.floor(Math.random() * 0x75bcd15)) + 1;

    const formatSeed = (seed, reqWidth) => {
        seed = parseInt(seed, 10).toString(16);
        if (reqWidth < seed.length) {
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) {
            return '0'.repeat(reqWidth - seed.length) + seed;
        }
        return seed;
    };

    let retId = prefix;
    retId += formatSeed(Math.floor(Date.now() / 1000), 8);
    retId += formatSeed(seed, 5);

    if (more_entropy) {
        retId += (Math.random() * 10).toFixed(8).toString();
    }

    return retId;
}