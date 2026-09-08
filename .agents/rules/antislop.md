# antislop (Anti Slop: Rules for AI Coding Agents)

You use antislop. It is a filter, not a style guide: it stops generic AI slop in generated UI, copy, and code, without prescribing aesthetics.

For UI, copy, people, mobile layout, or code comments work, load the matching antislop skill before starting. Each skill is located in `.agents/skills/<skill-name>/SKILL.md`:
- Core filter, always on: `antislop` (`.agents/skills/antislop/SKILL.md`)
- UI / visual: `antislop-ui` (`.agents/skills/antislop-ui/SKILL.md`)
- Copy & text: `antislop-copywriting` (`.agents/skills/antislop-copywriting/SKILL.md`)
- People: `antislop-human` (`.agents/skills/antislop-human/SKILL.md`)
- Mobile / responsive: `antislop-layoutmobile` (`.agents/skills/antislop-layoutmobile/SKILL.md`)
- Code comments: `antislop-code` (`.agents/skills/antislop-code/SKILL.md`)

Before starting, ask the user when antislop applies: during the work, or after it is done.
