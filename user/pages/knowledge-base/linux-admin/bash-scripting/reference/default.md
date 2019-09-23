---
visible: true
title: Bash Reference
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [bash,scripting]
---
[toc]
A reference for bash constructs with syntax and usage example for each construct.

### Quoting Variables to Prevent Word Splitting

Use double quotes when referencing a variable that has whitespace in it to prevent word splitting, and to escape special characters ( except `$`, backtick **`**, and `\`)

`List="one two three"`

```
List="one two three"

for a in $List     # Splits the variable in parts at whitespace.
do
  echo "$a"
done
# one
# two
# three

echo "---"

for a in "$List"   # Preserves whitespace in a single variable.
do #     ^     ^
  echo "$a"
done
# one two three
```

### Special Variables

- `${0}` - the script name
- `${1}`, `${2} ...` -  positional parameters passed from command line
- `${*}`, `${@}` - denote **all** positional parameters
- `$#` - denotes the number of positional parameters passed
- `$?` - denotes the exit code from a command




### The READ command

Interactively gather input from user by prompting with the read command

`read -p "Name a fictional character: " character_name`


    #!/bin/bash

    # Prompt the user for information.
    read -p "Name a fictional character: " character_name
    read -p "Name an actual location: " location
    read -p "What's your favorite food? " food

    # Compose the story.
    echo "Recently, ${character_name} was seen in ${location} eating ${food}!

### The Test Construct

The shorthand for the test command is the `[[ ]]` structure.

These are the things test can do:

- Check whether a file exists - `[[ -f ${file} ]]`
- Check whether a directory exists - `[[ -d ${dir} ]]`
- Check whether a variable is not empty - `[[ -n ${var} ]]`
- Check whether two variables have the same values - `[[ ${var1} == ${var2} ]]`
- Check whether FILE1 is older than FILE2 - `[[ ${file1} -ot ${file2} ]]`
- Check whether INTEGER1 is greater than INTEGER2 - `[[ ${int1} > ${int2} ]]`

```bash
#!/bin/bash
file_name=$1

# Check if the file exists.
if [[ -f ${file_name} ]]; then
  cat ${file_name} # Print the file content.
else
  echo "File does not exist, stopping the script!"
  exit 1
fi
```

### if-then statements

The if-then construct offers conditional execution of a command, while if-then-else offers an alternative if conditional is false. The if-then-elif offers multiple alternatives, though the `case` statement can better handle this.

!!! The boolean operators `||` and `&&` can often do what if-then does with more concise syntax.

- `if-then-exit` - if `[[ test ]]` then do; else exit
- `if-then-else` - if `[[- test ]]` then do; else do alternative
- `if-then-elif-else` - an if-then-else with multiple alternatives

**if-then-exit:**

```bash

#!/bin/bash

file_name=$1

# Check if the file exists.
if  -f ${file_name} ; then
  cat ${file_name} # Print the file content.
else
  echo "File does not exist, stopping the script!"
  exit 1
fi
```

**if-then-else:**

```bash

  #!/bin/bash

  file_name=$1

  # Check if the file exists.
  if  -f ${file_name} ; then
    cat ${file_name} # Print the file content.
  else
    echo "File does not exist, stopping the script!"
    exit 1
  fi
  ```

**if-then-elif-else:**

```bash
#!/bin/bash
count=99
if [ $count -eq 100 ]
then
  echo "Count is 100"
elif [ $count -gt 100 ]
then
  echo "Count is greater than 100"
else
  echo "Count is less than 100"
fi
```

### The `!`  `||` and `&&` boolean operators

- `!` is the NOT boolean operator to test for a condition being false
- `||` is the OR boolean operator. Only a **false** condition will cross it
- `&&` is the AND operator. Only a **true** condition will cross it.

The NOT operator:

    if [ ! -f $FILENAME ]
    then
    ...

The AND operator:

    if [ $condition1 ] && [ $condition2 ]
    #  Same as:  if [ $condition1 -a $condition2 ]
    #  Returns true if both condition1 and condition2 hold true...

    if [[ $condition1 && $condition2 ]]    # Also works.
    #  Note that && operator not permitted inside brackets
    #+ of [ ... ] construct.

The OR operator:

    if [ $condition1 ] || [ $condition2 ]
    # Same as:  if [ $condition1 -o $condition2 ]
    # Returns true if either condition1 or condition2 holds true...

    if [[ $condition1 || $condition2 ]]    # Also works.
    #  Note that || operator not permitted inside brackets
    #+ of a [ ... ] construct.
