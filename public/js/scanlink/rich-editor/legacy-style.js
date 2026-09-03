/**
 * Legacy CKEditor Font / Size / Text-Colour, as one TipTap mark.
 *
 * Loaded by Filament via `RichContentPlugin::getTipTapJsExtensions()`, which returns URLs and
 * resolves each with `(await import(url)).default`. TipTap MUST come from Filament's own bundle
 * — a second copy would break the `instanceof` checks ProseMirror relies on. Filament exposes
 * it on `window.FilamentRichEditor.tiptap` for exactly this, so this file needs no build step
 * and no npm dependency.
 *
 * Mirrors app/Filament/Forms/RichEditor/LegacyFormatting/Marks/LegacyStyleMark.php: one mark
 * with three attributes, rendered into a single `style` attribute. See that file for why the
 * three are combined rather than separate marks.
 */
const { Mark } = window.FilamentRichEditor.tiptap.core

const DECLARATIONS = {
    fontFamily: 'font-family',
    fontSize: 'font-size',
    color: 'color',
}

const read = (style) => {
    const found = {}

    for (const [name, property] of Object.entries(DECLARATIONS)) {
        const match = (style || '').match(
            new RegExp(`(?:^|;)\\s*${property}\\s*:\\s*([^;]+)`, 'i'),
        )

        const value = match ? match[1].trim() : ''

        if (value !== '') {
            found[name] = value
        }
    }

    return found
}

/**
 * Legacy writes nested spans as well as combined ones:
 *
 *     <span style="font-family:georgia"><span style="color:#003366">…
 *
 * Only one mark of a given type can apply to a range, so parsing each span in isolation makes
 * the inner one win and silently discards the outer declarations — you change the colour and
 * lose the font. Merging ancestors here (inner wins on conflict) means the innermost span
 * carries the full set and the outer, now-redundant marks collapse into it.
 */
/**
 * A wrapper span that only exists to hold a styled inner span — no text of its own.
 *
 * Legacy nests these (`<span style="font-family:x"><span style="color:y">text</span></span>`).
 * Both spans would otherwise create a mark over the same text; a mark type can only apply once
 * per range, so the two compete and whichever the editor reads first wins. Editing then loses
 * the other's declarations. Since readWithAncestors() already folds the outer declarations into
 * the inner mark, the wrapper is redundant and is skipped.
 *
 * It is only skipped when it has no direct text: a span with both its own text and a styled
 * child still needs its own mark, or that text loses its formatting.
 */
const isRedundantWrapperSpan = (element) => {
    const hasOwnText = [...element.childNodes].some(
        (node) => node.nodeType === 3 && node.textContent.trim() !== '',
    )

    if (hasOwnText) {
        return false
    }

    return [...element.querySelectorAll('span')].some(
        (span) => Object.keys(read(span.getAttribute('style'))).length > 0,
    )
}

// Legacy nests through other inline tags too, e.g.
//   <span style="color:#2F4F4F"><span style="font-size:20px"><strong><span …>
// so the walk has to step over <strong> rather than stop at it.
const INLINE_TAGS = new Set(['SPAN','STRONG','B','EM','I','U','S','STRIKE','SUB','SUP','A','FONT','SMALL','MARK'])

const readWithAncestors = (element) => {
    let merged = read(element.getAttribute('style'))

    for (
        let parent = element.parentElement;
        parent && INLINE_TAGS.has(parent.tagName);
        parent = parent.parentElement
    ) {
        if (parent.tagName === 'SPAN') {
            merged = { ...read(parent.getAttribute('style')), ...merged }
        }
    }

    return merged
}

export default Mark.create({
    name: 'legacyStyle',

    parseHTML() {
        return [
            {
                tag: 'span',
                getAttrs: (element) =>
                    !isRedundantWrapperSpan(element) &&
                    Object.keys(readWithAncestors(element)).length
                        ? {}
                        : false,
            },
        ]
    },

    addAttributes() {
        const attributes = {}

        for (const name of Object.keys(DECLARATIONS)) {
            attributes[name] = {
                default: null,
                parseHTML: (element) =>
                    readWithAncestors(element)[name] ?? null,
                // Emitted together in renderHTML so they share one style attribute.
                renderHTML: () => ({}),
            }
        }

        return attributes
    },

    renderHTML({ mark, HTMLAttributes }) {
        const declarations = []
        const attrs = { ...HTMLAttributes }

        // Read from mark.attrs, not HTMLAttributes: each attribute's own renderHTML returns
        // {} on purpose (so they do not each emit a competing `style`), which means the values
        // never reach HTMLAttributes.
        for (const [name, property] of Object.entries(DECLARATIONS)) {
            const value = mark?.attrs?.[name] ?? attrs[name]

            if (value) {
                declarations.push(`${property}:${value}`)
            }

            delete attrs[name]
        }

        if (declarations.length) {
            const existing = (attrs.style || '').trim().replace(/;$/, '')
            attrs.style = [declarations.join('; '), existing]
                .filter(Boolean)
                .join('; ')
        }

        return ['span', attrs, 0]
    },

    addCommands() {
        return {
            /**
             * Merge one declaration into the mark, keeping the others. Setting every
             * declaration to null removes the mark rather than leaving a bare span behind.
             */
            setLegacyStyle:
                (partial) =>
                ({ editor, commands }) => {
                    const merged = {
                        ...editor.getAttributes('legacyStyle'),
                        ...partial,
                    }

                    const hasAny = Object.keys(DECLARATIONS).some(
                        (name) => merged[name],
                    )

                    return hasAny
                        ? commands.setMark('legacyStyle', merged)
                        : commands.unsetMark('legacyStyle')
                },
            unsetLegacyStyle:
                () =>
                ({ commands }) =>
                    commands.unsetMark('legacyStyle'),
        }
    },
})
