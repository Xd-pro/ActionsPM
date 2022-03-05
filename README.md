# ActionsPM
A pocketmine plugin that allows other plugins to ask eachother to do things to a player through actions.

# The problem
Bob wants to create an NPC that opens a shop UI. He has an NPC plugin and a shop UI plugin. The normal way of going about this would be setting up the NPC to run the shop command on behalf of the player. This works, but what if the player didn't have permission to open the shop, but should still be able to use it through the NPC. Since PocketMine doesn't allow bypassing permissions on behalf of other players, there would be no way to do this.

# The solution
ActionsPM allows Bob to open the shop UI without having to deal with commands and permissions. ActionsPM is basically just a registry of functions that can be executed on players. So instead of the NPC running a command, it triggers and action on Bob that opens the UI.

# Using the API
First, add the plugin as a dependency in plugin.yml:
```yaml
depend: [ActionsPM]
```
this ensures that ActionsPM loads before your plugin.
otherwise, a fatal error will be thrown.
## API Main class: ActionsPM\ActionsPM\Actions
### `Actions::register(string $namespace, string $name, Action $action): void`
Register a new action that can be executed by other plugins or /trigger.

### `Actions::getNamespaces(): string[]`
Get a list of all namespaces. Useful when creating action selection forms.

### `Actions::getNamespaceActions(string $namespace): array[string => Action]`
Get a list of all actions in a certian namespace.

### `Actions::get(string $namespace, string $name): ?Action`
Get the closure of an action so that you can trigger it.

### `Actions::selectAction(Player $player, Closure /* function(Action $action, array $params): void */ $onComplete, Closure /* function(Player $player): void */ $onClose = null): void`
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
class MyAction extends Action {
    public function execute(Player $player, array $params) {
        // do whatever you want
    }
}

// in onEnable
self::register("namespace", "action", new MyAction());
```
### Using the action picker:
```php
Actions::selectAction($sender, function(Action $action, array $params) use ($player) {
    $action->execute($player, $params);
});
```

## Tip: IDE intellesense
### VSCode with Inteliphense
Clone the source of the plugin to a directory. Take note of the path. Next, in VSCode, go to Settings>Extensions>Inteliphense>Include Paths and paste the path. You should see a spinner on the blue bottom bar of VSCode that says "indexing".
### PhpStorm/IDEA Ultimate
TODO
