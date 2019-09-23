---
visible: true
title: Homework: Errors and Exception Handling
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [python]
---

### Problem 1
Handle the exception thrown by the code below by using <code>try</code> and <code>except</code> blocks.

My solution:


```python
for i in ['a','b','c']:
    try:
        print(i**2)
    except:
        print("Sorry cannot square strings")
        break
```

    Sorry cannot square strings


Course solution:


```python
try:
    for i in ['a','b','c']:
        print(i**2)
except:
    print("An error occurred!")
```

    An error occurred!


They put the for loop in the try block. My way allows for more, such as a success message if one of the values is an integer.

### Problem 2
Handle the exception thrown by the code below by using <code>try</code> and <code>except</code> blocks. Then use a <code>finally</code> block to print 'All Done.'

My solution:


```python
x = 5
y = 0
try:
    z = x/y
except:
    print("Cannot divide by zero")
finally:
    print("Alldone")
```

    Cannot divide by zero
    Alldone


Course solution:


```python
x = 5
y = 0
try:
    z = x/y
except ZeroDivisionError:
    print("Can't divide by Zero!")
finally:
    print('All Done!')
```

    Can't divide by Zero!
    All Done!


Now I know the format to specify the specific error condition.

### Problem 3
Write a function that asks for an integer and prints the square of it. Use a <code>while</code> loop with a <code>try</code>, <code>except</code>, <code>else</code> block to account for incorrect inputs.

My solution:


```python
def ask():
    while True:
        try:
            val = int(input("Enter a number: "))
            sqrnum = val**2
        except:
            print("An error occurred, pls try again")
            continue
        else:
            print(f'Thank you, your number squared is: {sqrnum}')
            break
```


```python
ask()
```

    Enter a number: 4
    Thank you, your number squared is: 16



```python
ask()
```

    Enter a number: r
    An error occurred, pls try again
    Enter a number:
    An error occurred, pls try again
    Enter a number: 5
    Thank you, your number squared is: 25


Course solution:


```python
def ask():

    while True:
        try:
            n = int(input('Input an integer: '))
        except:
            print('An error occurred! Please try again!')
            continue
        else:
            break


    print('Thank you, your number squared is: ',n**2)
```


```python
ask()
```

    Input an integer:
    An error occurred! Please try again!
    Input an integer: o
    An error occurred! Please try again!
    Input an integer: 6
    Thank you, your number squared is:  36


No need to perform the square calculation in try block. We just needed to test the input is an integer.
My version required an extra variable for the square.

**NOTE**  Another way to use a while loop:


```python
waiting = True
while waiting:
    try:
        something
    except:
        something
    else:
        waiting = False    # this is an alternative to using break here. It will end the loop also.
```

# Great Job!
