---
visible: true
title: PHP 7 Newest Features
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [programming,PHP]
---

## Scalar type declarations
Scalar type declaration means that the statement to a function to accept arguments (parameters) or return values only of the given scalar data type (int, float, string, bool, interfaces, array, callable).

*Argument type declarations* - To specify a scalar type declaration in an argument list, the name of the scalar data type should be added before the parameter name in the arguments. 
In 2 flavors: *coercive* (default) and *strict*.  
- *Coercive* mode example: 
The type listed in the parameter list will force the input to that type.

**Example**
```
<?php
function sum(int … $ints)
{
	return array_sum($ints);
}
print (sum(2, ‘3’, 4.1));
```  
will produce
``` 
9 (which is an int)
```

 - *Strict* mode example:
a single declare directive must be placed at the top of the file.  This enforces that the type of the parameters MUST match the type listed in the parameter list, or an error will be thrown.

**Example** 

```
<?php
	declare (strict_types=1)
	// rest of code
	function sum(int … $ints)
	{
		return array_sum($ints);
	}
	print (sum(2, ‘3’, 4.1)); 
``` 

will produce the error:
```
Fatal error: Uncaught TypeError: Argument 2 passed to sum() must be of the type integer, string given,
```
*Return type declarations* - these specify the type of the value that will be returned from a function.  The same types are allowed for return type declarations as for argument type declarations (int, float, string, bool, interfaces, array, callable)

**Example**
```
<?php
	function returnIntValue(int $value): int
	{
		return $value;
	}
	print(returnIntValue(5));
will produce
5 (which is an int)
```
**Another Example**
```
<?php
	declare(strict_types = 1);
	function returnIntValue(int $value): int
	{
		return $value + 1.0;
	}
	print(returnIntValue(5));
```

will produce the error:
```
Fatal error: Uncaught TypeError: Return value of returnIntValue() must be of the type integer, float returned…
```


## null coalescing operator ??
*??* is used to replace the *ternary* operation in conjunction with the *isset()* function. The null coalescing operator returns its first operand if it exists and is not NULL; otherwise it returns its second operand.
   
**Example**
```
<?php
// fetch the value of $_GET[‘user’] and returns ‘not passed’ if username is not passed
$username = $_GET[‘username’] ?? ‘not passed’;
print($username);
print(“<br/>”); 

// Equivalent code using ternary operator
$username =  isset($_GET[‘username’])  ?  $_GET[‘username’]  :  ‘not passed’;
print($username);
print(“<br/>”); 

// Chaining ?? operation
$username = $_GET[‘username’]  ??  $_POST[‘username’]  ??  ‘not passed’;
print($username);
```
produces the browser output:
```
not passed
not passed
not passed
```

## spaceship operator <=>
Used to compare two expressions.  It returns -1, 0, 1 when first expression is respectively less than, equal to, or greater than second expression.

**Example**
```
<?php
//integer comparison
print( 1 <=> 1); print(“<br/>”);
print( 1 <=> 2); print(“<br/>”);
print( 2 <=> 1); print(“<br/><br/>”);
//float comparison
print( 1.5 <=> 1.5); print(“<br/>”);
print( 1.5 <=> 2.5); print(“<br/>”);
print( 2.5 <=> 1.5); print(“<br/><br/>”);
//string comparison
print( ‘a’ <=> ‘a’); print(“<br/>”);
print( ‘a’ <=> ‘b’); print(“<br/>”);
print( ‘b’ <=> ‘a’); print(“<br/>”);
```
produces the following browser output
```
0
-1
1

0
-1
1

0
-1
1
```





#### Constant arrays
Array constants can now be defined using the *define()* function.  In PHP 5.6, they could only be defined using the *const* keyword.

**Example**
```
<?php
   //define a array using define function
   define('animals', [
      'dog',
      'cat',
      'bird'
   ]);
   print(animals[1]);
?>
```
Produces the following browser output -
```
cat
```
 

#### Anonymous classes 
Can now be defined using *new class*. 
Anonymous class can be used in place of a full class definition.

**Example**
```
<?php
   interface Logger {
      public function log(string $msg);
   }

   class Application {
      private $logger;

      public function getLogger(): Logger {
         return $this->logger;
      }

      public function setLogger(Logger $logger) {
         $this->logger = $logger;
      }  
   }

   $app = new Application;
   $app->setLogger(new class implements Logger {
      public function log(string $msg) {
         print($msg);
      }
   });

   $app->getLogger()->log("My first Log Message");
?>
```
   
It produces the following browser output −
```
My first Log Message
```

**Another example**
*pre-PHP 7*
```
<?php
	class Logger
	{
		public function log($msg)
		{
			echo $msg;
		}
	}
```

*PHP7+*
``` 
<?php
	$util->setLogger(new class{
		public function log($msg)
		{
			echo $msg;
		}
	});
```


**Unicode codepoint escape syntax** - This takes a Unicode codepoint in hex form, and outputs that codepoint in UTF-8 to a double-quoted string or a heredoc. Any valid codepoint is acceptable, with leading 0’s optional.
Examples
echo “\u{aa}”;   		//outputs:  #
echo “\u{0000aa}”;   	//outputs:  # (ignores leading zeroes)
echo “\u{202e}Reversed text”;   //outputs:  txet desreveR 
// to echo "mañana"; do either of the following:
echo "ma\u{00F1}ana";	 // pre-composed character ñ
// or
echo "man\u{0303}ana"; 	// "n" with combining ~ character (U+0303)

closure::call() - a shorthand way to temporarily bind an object scope to a closure and invoke it.  It is much faster in performance as compared to bindTo of PHP 5.6
Example - pre-PHP 7
<?php
   class A {
      private $x = 1;
   }

   // Define a closure pre-PHP 7 code
   $getValue = function() {
      return $this->x;
   };

   // Bind a clousure
   $value = $getValue->bindTo(new A, 'A'); // intermediate closure

   print($value());?>
It produces the following browser output −
1

Example - PHP 7+
<?php
   class A {
      private $x = 1;
   }

   // PHP 7+ code, Define
   $value = function() {
      return $this->x;
   };

   print($value->call(new A));?>
It produces the following browser output −
1

Filtered unserialize() function - provides better security when unserializing objects on untrusted data.  It prevents possible code injections and enable the developer to whitelist classes that can be unserialized.

Example
<?php
   class MyClass1 { 
      public $obj1prop;   
   }
   class MyClass2 {
      public $obj2prop;
   }

   $obj1 = new MyClass1();
   $obj1->obj1prop = 1;
   $obj2 = new MyClass2();
   $obj2->obj2prop = 2;

   $serializedObj1 = serialize($obj1);
   $serializedObj2 = serialize($obj2);

   // default behaviour that accepts all classes
   // second argument can be ommited.
   // if allowed_classes is passed as false, unserialize converts all objects 
// into __PHP_Incomplete_Class object
   $data = unserialize($serializedObj1 , ["allowed_classes" => true]);

   // converts all objects into __PHP_Incomplete_Class object except those of 
// MyClass1 and MyClass2
   $data2 = unserialize($serializedObj2 , ["allowed_classes" => ["MyClass1", "MyClass2"]]);

   print($data->obj1prop);
   print("<br/>");
   print($data2->obj2prop);?>
It produces the following browser output −
1
2

IntlChar class - class added which seeks to expose additional ICU functionality. This class defines a number of static methods and constants, which can be used to manipulate Unicode characters. You need to have Intl extension installed prior to using this class.

Example
<?php
   printf('%x', IntlChar::CODEPOINT_MAX);
   print (IntlChar::charName('@'));
   print(IntlChar::ispunct('!'));?>
It produces the following browser output −
10ffff
COMMERCIAL AT
true



CSPRNG - In PHP 7, following two new functions are introduced to generate cryptographically secure integers and strings in a cross platform way.
random_bytes() − Generates cryptographically secure pseudo-random bytes.
random_int() − Generates cryptographically secure pseudo-random integers.

random_bytes()
random_bytes() generates an arbitrary-length string of cryptographic random bytes that are suitable for cryptographic use, such as when generating salts, keys or initialization vectors.
Syntax
string random_bytes ( int $length )
Parameters
length − The length of the random string that should be returned in bytes.
Return Values
Returns a string containing the requested number of cryptographically secure random bytes.
Errors/Exceptions
If an appropriate source of randomness cannot be found, an Exception will be thrown.
If invalid parameters are given, a TypeError will be thrown.
If an invalid length of bytes is given, an Error will be thrown.

Example
<?php
   $bytes = random_bytes(5);
   print(bin2hex($bytes));?>
It produces the following browser output −
54cc305593

random_int()
random_int() generates cryptographic random integers that are suitable for use where unbiased results are critical.
Syntax
int random_int ( int $min , int $max )
Parameters
min − The lowest value to be returned, which must be PHP_INT_MIN or higher.
max - The highest value to be returned, which must be less than or equal to PHP_INT_MAX.
Return Values
Returns a cryptographically secure random integer in the range min to max, inclusive.
Errors/Exceptions
If an appropriate source of randomness cannot be found, an Exception will be thrown.
If invalid parameters are given, a TypeError will be thrown.
If max is less than min, an Error will be thrown.

Example
<?php
   print(random_int(100, 999));
   print("");
   print(random_int(-1000, 0));?>
It produces the following browser output −
614
-882




Expectations - a backward compatible enhancement to the older assert() function. Expectation allows for 	zero-cost assertions in production code, and provides the ability to throw custom exceptions when the 	assertion fails. assert() is now a language construct, where the first parameter is an expression as 	compared to being a string or Boolean to be tested.

Configuration directives for assert()
Directive	Default value	Possible values
zend.assertions	1	1 − generate and execute code (development mode)
0 − generate code but jump around it at runtime
-1 − do not generate code (production mode)
assert.exception	0	1 − throw, when the assertion fails, either by throwing the object provided as the exception or by throwing a new AssertionError object if exception was not provided.
0 − use or generate a Throwable as described above, but only generates a warning based on that object rather than throwing it (compatible with PHP 5 behaviour)
Parameters
assertion − The assertion. In PHP 5, this must be either a string to be evaluated or a Boolean to be tested. In PHP 7, this may also be any expression that returns a value, which will be executed and the result is used to indicate whether the assertion succeeded or failed.
description − An optional description that will be included in the failure message, if the assertion fails.
exception − In PHP 7, the second parameter can be a Throwable object instead of a descriptive string, in which case this is the object that will be thrown, if the assertion fails and the assert.exception configuration directive is enabled.
Return Values
FALSE if the assertion is false, TRUE otherwise.

Example
<?php
   ini_set('assert.exception', 1);

   class CustomError extends AssertionError {}

   assert(false, new CustomError('Custom Error Message!'));?>
It produces the following browser output −
Fatal error: Uncaught CustomError: Custom Error Message! in...

use statement - A single use statement can now be used to import classes, functions, and constants from the same namespace instead of multiple use statements.
Example
<?php
   // Before PHP 7
   use com\tutorialspoint\ClassA;
   use com\tutorialspoint\ClassB;
   use com\tutorialspoint\ClassC as C;

   use function com\tutorialspoint\fn_a;
   use function com\tutorialspoint\fn_b;
   use function com\tutorialspoint\fn_c;

   use const com\tutorialspoint\ConstA;
   use const com\tutorialspoint\ConstB;
   use const com\tutorialspoint\ConstC;

   // PHP 7+ code
   use com\tutorialspoint\{ClassA, ClassB, ClassC as C};
   use function com\tutorialspoint\{fn_a, fn_b, fn_c};
   use const com\tutorialspoint\{ConstA, ConstB, ConstC};
?>


Error handling -  error handling and reporting has been changed. Instead of reporting errors through the traditional error reporting mechanism used by PHP 5, now most errors are handled by throwing Error exceptions. Similar to exceptions, these Error exceptions bubble up until they reach the first matching catch block. If there are no matching blocks, then a default exception handler installed with set_exception_handler() will be called. In case there is no default exception handler, then the exception will be converted to a fatal error and will be handled like a traditional error.
As the Error hierarchy is not extended from Exception, code that uses catch (Exception $e) { ... } blocks to 	handle uncaught exceptions in PHP 5 will not handle such errors. A catch (Error $e) { ... } block or 	a set_exception_handler() handler is required to handle fatal errors.



Example
<?php
   class MathOperations {
      protected $n = 10;

      // Try to get the Division by Zero error object and display as Exception
      public function doOperation(): string {
         try {
            $value = $this->n % 0;
            return $value;
         } catch (DivisionByZeroError $e) {
            return $e->getMessage();
         }
      }
   }

   $mathOperationsObj = new MathOperations();
   print($mathOperationsObj->doOperation());?>

It produces the following browser output −
Modulo by zero

Integer division - introduces a new function intdiv(), which performs integer division of its operands and returns the division as int.




Example
<?php
   $value = intdiv(10,3);
   var_dump($value);
   print(" ");
   print($value);?>
It produces the following browser output −
int(3) 
3

Session options - From PHP7+, session_start() function accepts an array of options to override the session configuration directives set in php.ini. These options supports session.lazy_write, which is by default on and causes PHP to overwrite any session file if the session data has changed.
Another option added is read_and_close, which indicates that the session data should be read and then 	the session should immediately be closed unchanged. For example, Set session.cache_limiter to private 	and set the flag to close the session immediately after reading it, using the following code snippet.

Example
<?php
   session_start([
      'cache_limiter' => 'private',
      'read_and_close' => true,
   ]);?>

Deprecated features - Following features are deprecated and may be removed from future releases of PHP.
PHP 4 style constructors
PHP 4 style Constructors are methods having the same name as the class they are defined in, are now deprecated, and will be removed in the future. PHP 7 will emit E_DEPRECATED if a PHP 4 constructor is the only constructor defined within a class. Classes implementing a __construct() method are unaffected.
Example
<?php
   class A {
      function A() {
         print('Style Constructor');
      }
   }?>

It produces the following browser output −
Deprecated: Methods with the same name as their class will not be constructors 
in a future version of PHP; A has a deprecated constructor in...

Static calls to non-static methods
Static calls to non-static methods are deprecated, and may be removed in the future.
Example
<?php
   class A {
      function b() {
         print('Non-static call');
      }
   }
   A::b();?>



It produces the following browser output −
Deprecated: Non-static method A::b() should not be called statically in...
Non-static call

password_hash() salt option
The salt option for the password_hash() function has been deprecated so that the developers do not generate their own (usually insecure) salts. The function itself generates a cryptographically secure salt, when no salt is provided by the developer - thus custom salt generation is not required any more.

capture_session_meta SSL context option
The capture_session_meta SSL context option has been deprecated. SSL metadata is now used through the stream_get_meta_data() function.

Removed extensions & SAPIs - 
The following Extensions have been removed from PHP 7 onwards −
ereg
mssql
mysql
sybase_ct
The following SAPIs have been removed from PHP 7 onwards −
aolserver
apache
apache_hooks
apache2filter
caudium
continuity
isapi
milter
nsapi
phttpd
pi3web
roxen
thttpd
tux
webjames


