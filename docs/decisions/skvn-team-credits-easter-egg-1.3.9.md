# SKVN Team Credits Easter Egg - 1.3.9

Date: 2026-06-11
Status: approved requirement; implementation deferred
Milestone: V1 / 1.3.9

## Purpose

Close the V1 Slider work with a small, private tribute to the SKVN employees
who contributed to the project.

This is a credits/easter-egg milestone, not a public frontend feature.

## Approved Scope

### 1. Plugin Header Credit

Add this exact line to the plugin header comment in:

```text
wp-content/plugins/skvn-marine-blocks/skvn-marine-blocks.php
```

```text
Built with care by the SKVN team.
```

The line is informational only and must not change plugin metadata behavior.

### 2. Source/Asset Credit

Add this exact comment to the authored admin Easter egg source and ensure it is
present in the production admin asset:

```css
/* To the people who built SKVN, thank you. */
```

Do not add the message to public frontend HTML or the browser console.

### 3. SKVN Marine Admin Menu Easter Egg

The existing top-level `SKVN Marine` wp-admin menu is the trigger.

Behavior:

- A normal click continues to navigate normally.
- Five clicks within a short window trigger the credits experience.
- Do not block or delay normal WordPress menu navigation.
- Use `sessionStorage` so the click count can survive navigation/reload within
  the current admin session.
- Reset the count after timeout or after the Easter egg opens.
- On the fifth click, open an accessible wp-admin dialog/panel containing the
  approved tribute message.
- The exact tribute copy, names, nicknames, and visual treatment are finalized
  during milestone 1.3.9.
- Do not publish employee names without human confirmation that those people
  consent to being named.

## Technical Boundary

- Admin-only JavaScript and CSS.
- Enqueue only on relevant SKVN Marine admin pages where practical.
- No frontend asset.
- No database option.
- No AJAX or external network request.
- No analytics or click tracking.
- No credential or private employee data.
- No dependency addition.
- Do not change the existing menu slug or capability requirement.
- Keep keyboard and screen-reader access to the resulting dialog/panel.

## Acceptance Checklist

- [ ] Plugin header contains `Built with care by the SKVN team.`
- [ ] Authored/production admin asset contains the approved thank-you comment
- [ ] Normal SKVN Marine menu navigation still works
- [ ] Five clicks trigger the credits experience
- [ ] Click count survives menu navigation within the same admin session
- [ ] Timeout and successful activation reset the counter
- [ ] Credits UI is keyboard accessible and dismissible
- [ ] No frontend JS, CSS, HTML, or console output is added
- [ ] No database write, tracking, or network request occurs
- [ ] Human approves final message and any employee names before release
- [ ] PHP lint, plugin build, and wp-admin smoke test pass

