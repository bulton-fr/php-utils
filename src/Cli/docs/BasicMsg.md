# Utils\Cli\BasicMsg

This class allow you to sent message into the console.  
It's really basic (hence the name) and contain only the minimum.

To write a message, you can use methods `displayMsg` and `displayMsgNL`.
The first write a message without a line break. The second adds a line break automatically at the end of the message.

## Methods

### To display a message

__`void public static function displayMsg(string $msg, string $colorTxt = 'white', string $style = 'normal')`__

__`void public static function displayMsgNL(string $msg, string $colorTxt = 'white', string $style = 'normal')`__

These two methods will display a message with color and/or style.

The first method will just display the message, the second will add a line break (`\n`) at the end of the message.

Arguments are :

* `string $msg` : It's the message to display.
* `string $colorTxt` : It's the color of the text. Refer to the method `colorForShell` to know available color.
* `string $style` : It's the style of the text. Refer to the method `styleForShell` to know available color.

If there is only the first argument, the color and style will not be defined and stay with shell configuration at this moment.

After each message displayed, a call to the method `flush` is done.

__`void protected static function flush()`__

Call the php function `ob_flush()` if a buffer has been defined.

### To define color code to use in the shell

__`int protected static function colorForShell(string $color)`__

This method will return the color code to use in the shell for a string color name.

Available colors are :

* red
* green
* yellow
* white

If the color name into the argument not exist, the returned code will be for the `white` color.

### To define style code to use in the shell

__`int protected static function styleForShell(string $style)`__

This method will return the style code to use in the shell for a string style name.

Available styles are :

* normal
* bold

if the style name into the argument not exist, the returned code will be for the `normal` style.
