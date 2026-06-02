# Contributing to the UCF WordPress Block Theme

Thank you for your interest in contributing to this project!  If you are a developer for UCF and want to contribute to this theme, we'd love to hear from you.

This document outlines the best ways to submit new ideas or inform us of bugs.  Please take a moment to review these guidelines before submitting new issues or pull requests in order to make the contribution process easy and effective for everyone involved.

## Quick links
* [Using the issue tracker](#using-the-issue-tracker)
* [Bug reports](#bug-reports)
* [Feature requests](#feature-requests)
* [Pull requests](#pull-requests)
* [Asking questions/getting help](#asking-questionsgetting-help)
* [Code standards and style guides](#code-standards-and-style-guides)

-----

## Using the issue tracker

The [issue tracker](https://github.com/UCF/ucf-wordpress-block-theme/issues) in Github is the preferred channel for [bug reports](#bug-reports), [feature requests](#feature-requests) and [submitting pull requests](#pull-requests).

Please do not use the issue tracker for personal support requests.  The [#help-themes Teams channel](https://teams.microsoft.com/l/channel/19%3a8ade234408d44b93a02f01b1964d7856%40thread.skype/help-themes?groupId=5e3e72e2-3599-47b3-bcfc-bb3d5b579f12&tenantId=bb932f15-ef38-42ba-91fc-f3c59d5dd1f1) is the best place to get help with your project.  See the section on [getting help](#asking-questionsgetting-help) for more information.


## Bug reports

A bug is a demonstrable problem that is caused by the code in the repository. Concise and thorough bug reports will help us fix reported problems more quickly and effectively.

### Before submitting a bug report
Before you submit a new bug report, please follow these steps:

1. **Use the GitHub issue search** &mdash; check if the issue has already been reported.  Feel free to comment in the existing issue if it is still open and you have new information to share.

2. **Check if the issue has been fixed** &mdash; if you're not running the latest version of the theme, please check your code against the repo's `master` branch first (`master` will always contain the latest, stable project code). If you are running the latest version, make sure the problem isn't already resolved in an upcoming [milestone](https://github.com/UCF/ucf-wordpress-block-theme/milestones).

### Submit a bug report
If you've followed the steps above and have a valid bug report to submit, you can submit it by [creating a new issue in Github](https://github.com/UCF/ucf-wordpress-block-theme/issues/new?template=bug_report.md).

Add a descriptive, understandable title and details about the bug in the description field, following the template provided. Please try to be as detailed as possible in your report. What steps will reproduce the issue? What browser(s) and OS experience the problem? Do other browsers show the bug differently? What would you expect to be the outcome? All of the information you provide will help us quickly evaluate and fix the issue.

If you have a live example of the bug available somewhere public, please include a link in the bug report.  If you're not comfortable including the URL in the Github issue (e.g. it points to a development environment), you can make a note of it in the report (e.g. "see Teams for example URL"), then share the URL in the [#help-themes Teams channel](https://teams.microsoft.com/l/channel/19%3a8ade234408d44b93a02f01b1964d7856%40thread.skype/help-themes?groupId=5e3e72e2-3599-47b3-bcfc-bb3d5b579f12&tenantId=bb932f15-ef38-42ba-91fc-f3c59d5dd1f1).


## Feature requests

We welcome new feature requests from developers across campus.  Before submitting a new request, think carefully about if the proposed feature aligns with the goals of the project and with [UCF's brand](https://www.ucf.edu/brand/). We strongly encourage the discussion of new feature ideas in the [UCFTeam-CAMPUS-Wordpress Team](https://teams.microsoft.com/l/channel/19%3acf4fa1fd7b1042949ecd1acf5413aa22%40thread.skype/General?groupId=5e3e72e2-3599-47b3-bcfc-bb3d5b579f12&tenantId=bb932f15-ef38-42ba-91fc-f3c59d5dd1f1).

Please provide as much detail and context as possible to justify the inclusion of your idea in the theme. We reserve the right to deny feature requests when they don't align with the project's goals, or if said feature is already accomplishable with existing utilities/components.

Because this theme deliberately keeps interactive, JavaScript-dependent components in a companion plugin (see [roadmap.md](roadmap.md)), please confirm your request belongs in the theme's style layer rather than a plugin before submitting.

You can submit a new feature request by [creating a new issue in Github](https://github.com/UCF/ucf-wordpress-block-theme/issues/new?template=feature_request.md) and filling out the provided template.


## Pull requests

[**Please ask first**](#asking-questionsgetting-help) before embarking on any _significant_ pull request (e.g. implementing features, refactoring code); otherwise you risk spending a lot of time working on something that the theme's maintainers might not want to merge into the project. Pull requests should be related to existing issues that have been acknowledged by UCF Web Communications.

All pull requests should remain focused in scope and avoid containing unrelated commits.

Your pull request will be reviewed by at least one maintainer of the theme.  While your code should be complete enough to be understood by the person reviewing it, we don't want to spend an extensive amount of time reviewing code--try to keep your code sample brief enough to be reviewed within one hour.

Please adhere to the [coding guidelines](#code-standards-and-style-guides) used throughout the project (indentation, accurate comments, etc.)  Code that does not adhere to these standards will not be merged into the project.

### How to submit a pull request

Adhering to the following process is the best way to submit a pull request:

1. [Fork](https://help.github.com/articles/fork-a-repo/) the project.
2. Clone your fork, and configure the remotes:

   ```bash
   # Clone your fork of the repo into the current directory
   git clone https://github.com/<your-username>/ucf-wordpress-block-theme.git

   # Navigate to the newly cloned directory
   cd ucf-wordpress-block-theme

   # Assign the original repo to a remote called "upstream"
   git remote add upstream https://github.com/UCF/ucf-wordpress-block-theme.git
   ```

3. If you cloned a while ago, get the latest changes from upstream:

   ```bash
   git checkout master
   git pull upstream master
   ```

4. Create a new topic branch to contain your feature, change, or fix.

   ```bash
   git checkout -b <topic-branch-name>
   ```

    New branches **must** be branched off of the most recent existing `rc-*` branch (typically there will only be one open at a time), or off of `master` directly if no `rc-*` branch exists.

5. Commit your changes in logical chunks. Please provide [helpful, readable commit messages](https://chris.beams.io/posts/git-commit/) (avoid nondescriptive messages such as "bugfix" or "minor change").

    If you're making changes to Sass files, make sure you compile **and commit** the generated `assets/css/main.css`.  Style processing is performed using the npm scripts provided in the repo:

    ```bash
    npm install      # once, to install dependencies
    npm run build    # compile src/scss -> assets/css/main.css
    npm run watch    # recompile on save during development
    ```

6. Locally merge the upstream `rc-*` or `master` branch (whichever you branched off of initially) into your topic branch:

   ```bash
   git merge --no-ff upstream/master
   ```

7. Push your topic branch up to your fork:

   ```bash
   git push origin <topic-branch-name>
   ```

8. [Open a Pull Request](https://help.github.com/articles/about-pull-requests/) against the `rc-*` or `master` branch (whichever you initially branched off of.) Pull request titles and descriptions should be as descriptive and clear as possible.

-----

## Asking questions/getting help

In general, we keep conversations about distributed UCF WordPress projects on the [UCFTeam-CAMPUS-Wordpress Team](https://teams.microsoft.com/l/channel/19%3acf4fa1fd7b1042949ecd1acf5413aa22%40thread.skype/General?groupId=5e3e72e2-3599-47b3-bcfc-bb3d5b579f12&tenantId=bb932f15-ef38-42ba-91fc-f3c59d5dd1f1).  This workspace is owned and managed by UCF Web Communications (Webcom).  You'll need a **@ucf.edu** email address to join.

Whenever you post a question in this Team, please include your **job title**, information about the **college or department** you work for, as well as the **site(s) you need help with** to help us help you more effectively.

### General questions
If you have a general question about the theme, design decisions, or any other question that isn't a help request, please post it in the [#help-themes Teams channel](https://teams.microsoft.com/l/channel/19%3a8ade234408d44b93a02f01b1964d7856%40thread.skype/help-themes?groupId=5e3e72e2-3599-47b3-bcfc-bb3d5b579f12&tenantId=bb932f15-ef38-42ba-91fc-f3c59d5dd1f1).

### Help
If you need help with using this theme on your site, please drop us a line in the [#help-themes Teams channel](https://teams.microsoft.com/l/channel/19%3a8ade234408d44b93a02f01b1964d7856%40thread.skype/help-themes?groupId=5e3e72e2-3599-47b3-bcfc-bb3d5b579f12&tenantId=bb932f15-ef38-42ba-91fc-f3c59d5dd1f1).  You don't need to @mention anyone specific, but **please note that you're using the UCF WordPress Block Theme when posting your question** and include a link to your site if it's relevant to your question.

-----

## Code standards and style guides

Whenever you add or modify code in this repo, please follow the code style guides noted below, per language:

### PHP

[Adhere to the WordPress PHP Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/) for new or modified code.

### HTML / block markup

[Adhere to the mdo Code Guide.](http://codeguide.co/#html)

- Use tags and elements appropriate for an HTML5 doctype.
- Prefer core blocks, block templates, and block patterns over hardcoded markup where possible.
- Use [WAI-ARIA](https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA) attributes when appropriate to promote accessibility.

### CSS/Sass

Adhere to the [mdo Code Guide](http://codeguide.co/#css) for basic CSS formatting guidelines, except for what's noted below.

Use [CSS-Tricks' Sass Style Guide](https://css-tricks.com/sass-style-guide/) for Sass-specific features (e.g. order of extends, mixin inclusions, etc in a ruleset).

- All Sass is authored under `src/scss/` and compiled to `assets/css/main.css`. Do not edit the compiled CSS directly.
- Declaration order: declarations should be in alphabetical order.
- Selectors: all selectors in a ruleset should be on their own line.
- Prefer `theme.json` settings and design tokens (color palette, font sizes, spacing) over hardcoded values; reference presets via their `--wp--preset--*` CSS variables in Sass.
- All generated color palettes and font sizes/weights should comply with [WCAG 2.1 AA contrast guidelines](https://www.w3.org/TR/WCAG21/#contrast-minimum) in their default state.  Components and utilities with hover/focus/active states should try to comply with these contrast requirements whenever possible.
- Except in rare cases, don't remove default `:focus` styles (via e.g. `outline: none;`) without providing alternative styles. See [this A11Y Project post](https://www.a11yproject.com/posts/never-remove-css-outlines/) for more details.

### JS

[Adhere to the WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/) for any new or modified code.

- 2 spaces (no tabs).
- Prefer the [WordPress block editor APIs](https://developer.wordpress.org/block-editor/) and the [Interactivity API](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-interactivity/) over jQuery for new functionality.