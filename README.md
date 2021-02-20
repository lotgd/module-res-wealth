# Resource module: Wealth
![Tests](https://github.com/lotgd/module-res-wealth/workflows/Tests/badge.svg)

A simple resource module providing a common API to track gold and wealth of a user.

## API
### Events
- `h/lotgd/module-res-wealth/gold/increment` (`Module::HookIncrementGold`)\
  This hook allows to change the amount of gold a character earns. Could be used to change 
  difficulty or grind require.

- `h/lotgd/module-res-wealth/gems/increment` (`Module::HookIncrementGems`)\
  This hook allows to change the amount of gems a character earns.

### Character Model Extension Methods
- `getGold(): int`\
  Returns the amount of gold a character owns.

- `setGold(int $amount): void`\
  Sets the amount of gold a character owns.

- `addGold(int $amount): int`\
  Increased (or decreases if negative) the amount of gold a user owns. Since this method publishes
  a hook, it also returns the actual amount of gold added, allowing modules to react on changes.

- `getGems(): int`\
  Returns the amount of gems a character owns.

- `setGems(int $amount): void`\
  Sets the amount of gems a character owns.

- `addGems(int $amount): int`\
  Increased (or decreases if negative) the amount of gems a user owns. Since this method publishes
  a hook, it also returns the actual amount of gold added, allowing modules to react on changes.

### Character Properties
- `lotgd/module-res-wealth/gold` (`Module::CharacterPropertyGold`)
  Property to keep track of the amount of gold a user owns.
  Can be configured via command line (see `>daenerys character:config:list`)

- `lotgd/module-res-wealth/gems` (`Module::CharacterPropertyGems`)
  Property to keep track of the amount of gems a user owns.
  Can be configured via command line (see `>daenerys character:config:list`)