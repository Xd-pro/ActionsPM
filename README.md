# ActionsPM
A pocketmine plugin that allows other plugins to ask eachother to do things to a player through actions.

# The problem
Bob wants to create an NPC that opens a shop UI. He has an NPC plugin and a shop UI plugin. The normal way of going about this would be setting up the NPC to run the shop command on behalf of the player. This works, but what if the player didn't have permission to open the shop, but should still be able to use it through the NPC. Since PocketMine doesn't allow bypassing permissions on behalf of other players, there would be no way to do this.

# The solution
ActionsPM allows Bob to open the shop UI without having to deal with commands and permissions. ActionsPM is basically just a registry of functions that can be executed on players. So instead of the NPC running a command, it triggers and action on Bob that opens the UI.

# Config actions
ActionsPM allows you to create actions without code (or with it, of course) using config files. Here are a few examples:
```yaml
---
hello_world:
  type: message
  message: Hello World!
two_hello_worlds:
  type: multiple
  actions:
  - namespace: actionspm
    action: hello_world
  - namespace: actionspm
    action: hello_world
sample_tp:
  type: teleport
  x: 0
  y: 64
  z: 0
  world: world
  yaw: 0
  pitch: 0
...
```
All actions created in config files use the namespace "actionspm".

# Using the API
First, add the plugin as a dependency in plugin.yml:
```yaml
depend: [ActionsPM]
```
this ensures that ActionsPM loads before your plugin. If you want to make ActionsPM optional, add it as an optional dependency:
```yaml
softdepend: [ActionsPM]
```
When using `softdepend`, before attempting to call any API functions, check that ActionsPM is loaded:
```php
if ($this->getServer()->getPluginManager()->getPlugin("ActionsPM")) {
  // use API
}
```
otherwise, a fatal error will be thrown.
## API Main class: ActionsPM\ActionsPM\Actions
### `Actions::register(string $namespace, string $name, Closure /* function(Player $player): any */ $action): void`
Register a new action that can be executed by other plugins or /trigger.

### `Actions::getNamespaces(): string[]`
Get a list of all namespaces. Useful when creating action selection forms.

### `Actions::getNamespaceActions(string $namespace): array[string => Closure /* function(Player $player): any */]`
Get a list of all actions in a certian namespace.

### `Actions::get(string $namespace, string $name): ?Closure`
Get the closure of an action so that you can trigger it.

### `Actions::selectAction(Player $player, Closure /* function(string $namespace, string $name): void */ $onComplete, Closure /* function(Player $player): void */ $onClose = null): void`
Use a form to pick an action.

## Examples
### Triggering an action: 
```php
if (Actions::get("namepspace", "action") !== null) {
    Actions::get("namepspace", "action")($player);
}
```
### Creating an action: 
```php
Actions::register("namespace", "action", function(Player $player) {
    // do whatever you want to the player
});
```
### Using the action picker:
```php
Actions::selectAction($sender, function(string $namespace, string $action) use ($sender) {
  $sender->sendMessage("Picked $namespace:$action");
}, function(Player $player) use ($sender): void {
  $sender->sendMessage("Closed");
});
```

## Tip: IDE intellesense
### VSCode with Inteliphense
Clone the source of the plugin to a directory. Take note of the path. Next, in VSCode, go to Settings>Extensions>Inteliphense>Include Paths and paste the path. You should see a spinner on the blue bottom bar of VSCode that says "indexing".
### PhpStorm/IDEA Ultimate
TODO
