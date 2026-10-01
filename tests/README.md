# Tests

One tier today: a **PHP unit suite** that runs without WordPress.

```bash
npm test              # or: composer test
composer test:unit    # the same suite, named explicitly
```

It needs no database, no Docker and no wp-env, and finishes in well under a second. That is
the property worth protecting — a suite that needs a container is one nobody runs before
committing, which is the same as not having it.

## What belongs here

Functions that are string transforms, registration calls, or markup builders. Everything
currently covered takes arguments and returns a value, with WordPress stubbed at the edges.

**What does not belong here:** anything that genuinely needs WordPress — a meta query against
real posts, a `WP_HTML_Tag_Processor` walk, the `render_block` filter, a real
`register_block_type()`. Do **not** mock those into this suite. A test that mocks the thing it
is testing tests the mock. That work belongs in an integration tier running against real
WordPress under wp-env, which this theme does not have yet; adding one is the next step when
something needs it.

## Layout

```
bootstrap.php     Defines ABSPATH and UCF_THEME_DIR, loads the autoloader and the stubs
TestCase.php      Brain Monkey setup/teardown, plus loadInclude()
stubs/
  wp-functions.php  WordPress functions with no behavior worth varying per test
*Test.php         One file per topic, named for the include it covers
```

## How the harness works

**The theme's includes are loaded per-test, not by the bootstrap.** Every include calls
`add_action()` or `add_filter()` at include time, and those functions only exist while Brain
Monkey is running — which is per-test. So a test asks for what it needs in `setUp()`:

```php
protected function setUp(): void {
    parent::setUp();
    $this->loadInclude( 'university-header' );
}
```

`loadInclude()` is idempotent, because PHP function definitions persist for the whole process.
One consequence is worth knowing: **the hooks registered at include time are registered during
exactly one test's Brain Monkey session** — whichever test loaded the file first. So do not
assert that a hook is attached. Call the hooked function directly and assert on what it does,
which is what every test here does.

**Two kinds of stub, on purpose:**

-   Functions with no behavior worth varying — `__()` today — are real definitions in
    `stubs/wp-functions.php`, loaded once. Add one there **when a test needs it, not before**:
    that file previously held eight, seven of which nothing called.
-   Functions whose return value a test needs to _control_ — `wp_enqueue_script()`,
    `register_block_style()`, `get_post_meta()` — are stubbed per test with Brain Monkey, in the
    test that cares.

## Adding a test

**New code in `includes/` ships with its test.** Nothing enforces this; it is a habit. Put the
test with the file that owns the topic.

**PHPUnit is pinned to `^9.6`, deliberately.** It is the version WordPress core's own test
suite supports, so the project stays on one toolchain if an integration tier is ever added.
Write `@dataProvider` / `@covers` annotations, not `#[DataProvider]` attributes.

**Prove a new test can fail.** Break the code it covers, watch it go red, then restore. This is
not ceremony — a test that passes against nothing at all looks exactly like a passing test. All
four assertions in `BlockStylesTest` were verified this way against deliberate mutations
(a composition added to one file and not the other, a misspelled treatment, a style registered
on the wrong block type).

## What each file covers

| File                   | Covers                                                            |
| ---------------------- | ----------------------------------------------------------------- |
| `PasteArtifactsTest`   | The substitution table — what is replaced _and_ what must survive |
| `UniversityHeaderTest` | The contract with a host the theme does not control               |
| `BlockStylesTest`      | That `block-styles.php` and `_compositions.scss` still agree      |
| `SearchServiceTest`    | The Search Service cache: fresh, stale, and remembered failure    |
| `DegreeTest`           | Program fields as bound values, and each degree block's markup    |

Two of these exist because the failure they guard is **silent**:

-   The University Header renders either way — just boxed at 940px instead of full width, or in
    a spot nothing chose. A page capture cannot tell the two apart, and neither can a reviewer.
-   A composition registered in PHP but absent from the Sass map is an entry in the editor's
    Styles panel that paints nothing; one in Sass but not PHP is CSS no editor can reach. Both
    files carry a `SYNC:` comment about it, which is an instruction to a human and catches
    nothing. `BlockStylesTest` is the only thing that looks.

## Tiers that do not exist yet

Worth adding, in roughly this order:

1. **A Jest markup sweep** over `templates/` and `parts/`, asserting every block parses to
   valid markup. Invalid blocks still _render_ on the front end, so a page that looks right
   proves nothing — this is the only practical way to catch it. Use
   `isValidBlockContent()`, never a block's `isValid` flag: `parse()` recovers mismatched
   markup by migrating it through the block type's `deprecated` array and then reports
   `isValid: true`.
2. **An integration tier** under wp-env, for anything needing real WordPress.
3. **An accessibility tier** — Playwright + axe — once there are patterns and blocks to audit.
