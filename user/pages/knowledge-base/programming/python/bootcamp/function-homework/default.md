---
visible: true
title: Function Homework
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [python,bootcamp,functions]
---

This is an excerpt from the Python Bootcamp course, of the section called Functions and Methods Homework

## Functions and Methods Homework

Complete the following questions:
____
### Write a function that computes the volume of a sphere given its radius.


```python
from fractions import Fraction
f = Fraction(4,3)
def vol(rad):
    return float(f * (3.14 * (rad)**3))
```


```python
# Check
vol(2)
```




    33.49333333333333



___
### Write a function that checks whether a number is in a given range (inclusive of high and low)


```python
def ran_check(num,low,high):
    if num in range(low,high):
        print(f'{num} is in the range between {low} and {high}')
    else:
        print(f'{num} is not in the range between {low} and {high}')
```


```python
# Check
ran_check(11,2,7)
```

    11 is not in the range between 2 and 7


If you only wanted to return a boolean:


```python
def ran_bool(num,low,high):
    return num in range(low,high)

```


```python
ran_bool(3,1,10)
```




    True



____
### Write a Python function that accepts a string and calculates the number of upper case letters and lower case letters.

    Sample String : 'Hello Mr. Rogers, how are you this fine Tuesday?'
    Expected Output :
    No. of Upper case characters : 4
    No. of Lower case Characters : 33

HINT: Two string methods that might prove useful: **.isupper()** and **.islower()**

If you feel ambitious, explore the Collections module to solve this problem!


```python
def up_low(s):
    uppers = 0
    lowers = 0
    print(f'Original String: {s}')
    for char in (s):
        if char.isupper():
            uppers += 1
        else:
            if char.islower():
                lowers += 1
    print(f'No. of Upper case characters: {uppers}')
    print(f'No. of Lower case characters: {lowers}')
```


```python
s = 'Hello Mr. Rogers, how are you this fine Tuesday?'
up_low(s)
```

    Original String: Hello Mr. Rogers, how are you this fine Tuesday?
    No. of Upper case characters: 4
    No. of Lower case characters: 33


____
### Write a Python function that takes a list and returns a new list with unique elements of the first list.

    Sample List : [1,1,1,1,2,2,3,3,3,3,4,5]
    Unique List : [1, 2, 3, 4, 5]


```python
def unique_list(lst):
    newlist = []
    for x in lst:
        if x in newlist:
            pass
        else:
            newlist.append(x)
    print('Unique List: {}'.format(newlist))
```


```python
unique_list([1,1,1,1,2,2,3,3,3,3,4,5])
```

    Unique List: [1, 2, 3, 4, 5]


____
### Write a Python function to multiply all the numbers in a list.

    Sample List : [1, 2, 3, -4]
    Expected Output : -24


```python
def multiply(numbers):
    bin = 1
    for n in numbers:
        bin *= n
    return bin
```


```python
multiply([1,2,3,-4])
```




    -24



____
### Write a Python function that checks whether a passed in string is palindrome or not.

Note: A palindrome is word, phrase, or sequence that reads the same backward as forward, e.g., madam or nurses run.


```python
def palindrome(s):
    return s == s[::-1]
```


```python
palindrome('helleh')
```




    True



____
### Hard: Write a Python function to check whether a string is pangram or not.

    Note : Pangrams are words or sentences containing every letter of the alphabet at least once.
    For example : "The quick brown fox jumps over the lazy dog"

Hint: Look at the string module


```python
import string
def ispangram(str):
    abet = string.ascii_lowercase
    for c in abet:
        if c in str:
            continue
        else:
            return False
    return True
```


```python
ispangram("The quick brown fox jumps over the lazy dog")
```




    True




```python
string.ascii_lowercase
```




    'abcdefghijklmnopqrstuvwxyz'




```python

```

Here is the instructor solution using the set() function:

.


```python
import string

def ispangram(str1, alphabet=string.ascii_lowercase):
    alphaset = set(alphabet)
    print(alphaset)         # i inserted this for testing, see below
    print(set(str1.lower()))      # i inserted this for testing, see below
    return alphaset <= set(str1.lower())

```


```python
ispangram("The quick brown fox jumps over the lazy dog")
```

    {'e', 'j', 'n', 'x', 'b', 'c', 'h', 'l', 'm', 'k', 'f', 'q', 'y', 'o', 'z', 's', 'd', 't', 'a', 'u', 'p', 'v', 'w', 'i', 'g', 'r'}
    {'e', 'j', 'n', 'x', 'b', 'c', 'h', 'l', 'm', 'k', 'f', 'q', ' ', 'y', 'o', 'z', 's', 'd', 't', 'a', 'u', 'p', 'w', 'v', 'i', 'g', 'r'}





    True



I inserted the print statements to find out how set() transformed the str1 argument compared to the value of alphaset.

.

The function works because the set() function creates a set of every original character in a string passed to it.

A string with all characters in the alphabet will be transformed by set() into a set of exactly 26 characters, or 27 if it had spaces.

The alphaset will have exactly 26 characters. It will either be one less than str1 or exactly equal to it, so the less than equal operator will return the correct boolean.
