"""Check the theme and plugin PHP for the mistakes that take wp-admin down.

There is no PHP binary on the build machine, so `php -l` is not available and a
parse error can only be found by reading the source the way PHP reads it. This
walks each file quote by quote - strings, comments and heredocs first, code
second - which is the order that matters, because everything else is decided by
where the strings end.

It looks for four things:

* an apostrophe inside a single-quoted string. This is the one that took every
  wp-admin page down on 2026-09-10. `'editing one product's page'` closes the
  string at the apostrophe, so `s page cannot change another` becomes code and
  the file stops parsing. Nothing is left unbalanced - the quotes still pair up
  and so do the brackets - so the giveaway is the adjacency: a string boundary
  welded to a word character. Real PHP never opens a string straight after a
  letter, and never closes one straight before one.
* an unterminated string, block comment or heredoc.
* brackets that do not balance, counted only over code.
* a closing bracket that does not match the one it closes.

    python wordpress/tools/php-syntax-check.py [path ...]

Defaults to wordpress/wp-content. Prints one line per problem and exits 1 if
there are any, so it can gate the packaging step. Only the plugin and the child
theme are ours; WordPress core, WooCommerce and Elementor are not versioned here
and are not checked.
"""

import os
import re
import sys

HERE = os.path.dirname(os.path.abspath(__file__))
WORDPRESS = os.path.dirname(HERE)

BACKSLASH = chr(92)
WORD_TAIL = re.compile(r"[A-Za-z0-9_]")
HEREDOC = re.compile(
    r"<<<[ \t]*(?:\"([A-Za-z_]\w*)\"|'([A-Za-z_]\w*)'|([A-Za-z_]\w*))\r?\n"
)
CLOSES = {")": "(", "]": "[", "}": "{"}


def check(path):
    """Return a list of (line, message) for one PHP file."""
    with open(path, encoding="utf-8", errors="replace") as handle:
        source = handle.read()

    problems = []
    stack = []
    index = 0
    end = len(source)
    in_php = False

    def line_of(position):
        return source.count("\n", 0, position) + 1

    while index < end:
        if not in_php:
            # Outside <?php ... ?> everything is literal output.
            long_tag = source.find("<?php", index)
            short_echo = source.find("<?=", index)
            if long_tag == -1 and short_echo == -1:
                break
            if long_tag == -1 or (short_echo != -1 and short_echo < long_tag):
                index = short_echo + 3
            else:
                index = long_tag + 5
            in_php = True
            continue

        char = source[index]

        if source.startswith("?>", index):
            in_php = False
            index += 2
            continue

        if source.startswith("#[", index):  # attribute, not a comment
            index += 2
            continue

        if source.startswith("//", index) or char == "#":
            while index < end and source[index] != "\n":
                if source.startswith("?>", index):
                    break
                index += 1
            continue

        if source.startswith("/*", index):
            close = source.find("*/", index + 2)
            if close == -1:
                problems.append((line_of(index), "unterminated /* comment"))
                break
            index = close + 2
            continue

        if char in ("'", '"'):
            before = source[index - 1] if index else ""
            if WORD_TAIL.match(before):
                problems.append((
                    line_of(index),
                    "string opens immediately after %r - stray apostrophe?" % before,
                ))
            cursor = index + 1
            closed = False
            while cursor < end:
                if source[cursor] == BACKSLASH:
                    cursor += 2
                    continue
                if source[cursor] == char:
                    closed = True
                    break
                cursor += 1
            if not closed:
                problems.append((line_of(index), "unterminated %s string" % char))
                break
            after = source[cursor + 1] if cursor + 1 < end else ""
            if WORD_TAIL.match(after):
                problems.append((
                    line_of(cursor),
                    "string closes immediately before %r - stray apostrophe?" % after,
                ))
            index = cursor + 1
            continue

        heredoc = HEREDOC.match(source, index)
        if heredoc:
            label = heredoc.group(1) or heredoc.group(2) or heredoc.group(3)
            body = source[heredoc.end():]
            closing = re.search(r"^[ \t]*" + label + r"\b", body, re.M)
            if not closing:
                problems.append((line_of(index), "unterminated heredoc %s" % label))
                break
            index = heredoc.end() + closing.end()
            continue

        if char in "([{":
            stack.append((char, line_of(index)))
        elif char in CLOSES:
            if not stack:
                problems.append((line_of(index), "unmatched closing %s" % char))
            elif stack[-1][0] != CLOSES[char]:
                opener, opened_at = stack.pop()
                problems.append((
                    line_of(index),
                    "closing %s but %s was opened on line %d" % (char, opener, opened_at),
                ))
            else:
                stack.pop()
        index += 1

    if stack:
        opener, opened_at = stack[-1]
        problems.append((
            opened_at,
            "%s never closed (%d still open at end of file)" % (opener, len(stack)),
        ))

    problems.sort()
    return problems


def label_for(path):
    """Name a file relative to wordpress/ when it lives there, else in full."""
    try:
        return os.path.relpath(path, WORDPRESS).replace(os.sep, "/")
    except ValueError:  # a different drive on Windows
        return path.replace(os.sep, "/")


def run(roots):
    """Check every .php file under roots. Returns the number of bad files."""
    checked = 0
    bad = 0
    for root in roots:
        for dirpath, dirnames, filenames in os.walk(root):
            dirnames[:] = sorted(d for d in dirnames if d != "__pycache__")
            for filename in sorted(filenames):
                if not filename.endswith(".php"):
                    continue
                path = os.path.join(dirpath, filename)
                checked += 1
                problems = check(path)
                if problems:
                    bad += 1
                    for line, message in problems:
                        print("  %s:%d: %s" % (label_for(path), line, message))
    print("  %d PHP files checked, %d with problems" % (checked, bad))
    return bad


def main():
    roots = sys.argv[1:] or [os.path.join(WORDPRESS, "wp-content")]
    return 1 if run(roots) else 0


if __name__ == "__main__":
    sys.exit(main())
