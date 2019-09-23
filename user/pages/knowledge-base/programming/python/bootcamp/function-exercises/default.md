---
visible: true
title: Function Exercises
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [python,programming]
---

This section is excerpted from the Udemy course Complete Python 3 Bootcamp.

[toc]

## Function Practice Exercises

All exercises in this section are posted in the following format:
- the real world problem
- a sample of expected output in boldface
- the technical gist of what this function needs to do
- my solution which satisfied the requirement
- the course solution

### Warmup Section

#### LESSER OF TWO EVENS
 Write a function that returns the lesser of two given numbers if both numbers are even, but returns the greater if one or both numbers are odd.

**lesser_of_two_evens(2,4) --> 2**</br>
**lesser_of_two_evens(2,5) --> 5**

The function needs to evaluate both arguments for even numbers, then conditionally return one of 2 possible results depending on the first evaluation.

##### my solution
    def lesser_of_two_evens(a,b):
        if a % 2 == 0 and b % 2 == 0:
            if a > b:
                return b
            else:
                return a
        else:
            if a > b:
                return a
            else:
                return b

</br>

##### course solution
    def lesser_of_two_evens(a,b):       # got this part right, check for even using modulo
        if a%2 == 0 and b%2 == 0:
            return min(a,b)
        else:
            return max(a,b)       # using min() and max() means no nested if-else statements

</br>

#### ANIMAL CRACKERS
 Write a function takes a two-word string and returns True if both words begin with same letter

**animal_crackers('Levelheaded Llama') --> True**</br>
**animal_crackers('Crazy Kangaroo') --> False**

The function needs to evaluate two indexes in a string for equality. Since the string length and therefore the indexes are not known, the string needs to be split into a list so we know to use index 0 on both elements.

##### my solution
    def animal_crackers(text):
        mylist = text.split()
        return mylist[0][0].lower() == mylist[1][0].lower()

</br>
##### course solution
    def animal_crackers(text):
        wordlist = text.lower().split()           # force the entire string to lower before splitting
        return wordlist[0][0] == wordlist[1][0]

</br>

#### MAKES TWENTY
 Given two integers, return True if the sum of the integers is 20 or if one of the integers is 20. If not, return False

**makes_twenty(20,10) --> True**</br>
**makes_twenty(12,8) --> True**</br>
**makes_twenty(2,3) --> False**</br>

The function needs to return the result of a boolean **or** evaluation.

##### my solution
    def makes_twenty(n1,n2):
        return n1 + n2 == 20 or n1 == 20 or n2 == 20

</br>

##### course solution
    def makes_twenty(n1,n2):
        return (n1+n2)==20 or n1==20 or n2==20    # nailed it

</br>

### Level 1 Problems

#### OLD MACDONALD
 Write a function that capitalizes the first and fourth letters of a name

**old_macdonald('macdonald') --> MacDonald**</br>

The function needs to iterate thru a string and return a mutated form of the string.

##### my solution

    def old_macdonald(name):
        newstring = ''
        for c in name:
            if c == name[0]:
                newstring = newstring + (c.upper())
            elif c == name[3]:
                newstring = newstring + (c.upper())
            else:
                newstring = newstring + (c)
        return newstring

    old_macdonald('macdonald')
    'MacDonalD'                     # # does not completely work

</br>

##### course solution

    def old_macdonald(name):
        if len(name) > 3:
            return name[:3].capitalize() + name[3:].capitalize()   # see below
        else:
            return 'Name is too short!'


The **capitalize()** function only caps the first letter of a string, so a range of indexes works here.
- **name[:3]** is everything up to (not including) index 3 which is effectively the first 3 letters
- **name[3:]** is everything starting at (including) index 3 which is effectively the 4th letter and all after
- since the .capitalize() function capitalizes the first letter of a string, it will effectively treat both index ranges independently, and the end result is what we want.

</br>

#### MASTER YODA
 Given a sentence, return a sentence with the words reversed

**master_yoda('I am home') --> 'home am I'**</br>
**master_yoda('We are ready') --> 'ready are We'**

The function needs to split a string and return the elements in a reverse order.

##### my solution

    def master_yoda(text):
        newlist = text.split()
        return " ".join(newlist[-1::-1])

</br>

##### course solution

    def master_yoda(text):
        return ' '.join(text.split()[::-1])   # see below


The **.join()** function can take the **test.split** argument, so did not a new list. Also, did not need the start index, just the -1 step.

</br>

#### ALMOST THERE
 Given an integer n, return True if n is within 10 of either 100 or 200

**almost_there(90) --> True**</br>
**almost_there(104) --> True**</br>
**almost_there(150) --> False**</br>
**almost_there(209) --> True**

The function needs to return the result a boolean **or** evaluation.

##### my solution

    def almost_there(n):
        return abs(n - 100) <= 10 or abs(n - 200) <= 10

</br>

##### course solution

    def almost_there(n):
        return ((abs(100 - n) <= 10) or (abs(200 - n) <= 10))   # see below

Although mine still worked, i didn't wrap the whole boolean test in a paren, and did the subtraction backwards, which worked because of **abs()**.

</br>

### Level 2 problems

#### FIND 33
Given a list of ints, return True if the array contains a 3 next to a 3 somewhere.

**has_33([1, 3, 3]) → True**</br>
**has_33([1, 3, 1, 3]) → False**</br>
**has_33([3, 1, 3]) → False**</br>

The function needs to iterate thru a sequence and look for 2 consecutive occurrences of a stated value.

##### my solution

    def has_33(nums):
        count = 0
        num1 = 0
        num2 = 0
        for num in nums:
            if num1 == 0 or count % 2 == 0:
                num1 = num
            else:
                num2 = num
            if num1 == num2:
                return True
            count += 1
        return False

</br>

##### course solution

    def has_33(nums):
        for i in range(0, len(nums)-1):  # check every number EXCEPT the last one, hence the -1. (see below)

    # nicer looking alternative in commented code
    #if nums[i] == 3 and nums[i+1] == 3:            # directly compare 2 indexes

    if nums[i:i+2] == [3,3]:        # use slicing to compare current index (i) to (i+2)
        return True

    return False

I did a whole lot of unnecessary work here. My approach was to establish a way to assign 2 iterations to variables and compare, then start the assignment over, using a counter.

The right way is to refer to the current iteration i as an index of **(nums)** and check it against the next index within **(nums)**.

Why use the **.range()** function? To remove the last number from the iterations.

Since the function checks every number against the next number, it cannot check the last number, and so the **range(0, len(nums)-1)** removes the last number from consideration.

Once the range is established the function simply compares 2 adjacent indexes together for equality

**NOTE**: the slicing method **[i:i+2]**, slicing is up to but NOT including, hence the +2, which translates to the next index.

</br>

#### PAPER DOLL
Given a string, return a string where for every character in the original there are three characters

**paper_doll('Hello') --> 'HHHeeellllllooo'**</br>
**paper_doll('Mississippi') --> 'MMMiiissssssiiippppppiii'**</br>

The function needs to mutate a string.

##### my solution

    def paper_doll(text):
        newstring = ''
        for c in text:
            newstring = newstring + c*3
        return newstring

</br>

##### course solution

    def paper_doll(text):
        result = ''
        for char in text:
            result += char * 3
        return result

</br>

I could have used the **+=** incremental operator rather than reassigning the string on each iteration.
</br>

#### BLACKJACK
Given three integers between 1 and 11, if their sum is less than or equal to 21, return their sum. If their sum exceeds 21 and there's an eleven, reduce the total sum by 10. Finally, if the sum (even after adjustment) exceeds 21, return 'BUST'

**blackjack(5,6,7) --> 18**</br>
**blackjack(9,9,9) --> 'BUST'**</br>
**blackjack(9,9,11) --> 19**

This function needs to first sum all arguments, then evaluate the return based on the the sum as well as the presence of a stated value.

##### my solution

    def blackjack(a,b,c):
        for num in (a,b,c):
            if (a+b+c) > 21 and num == 11:
                newnum = (a+b+c) - 10
                if newnum > 21:
                    return 'BUST'
                else:
                    return newnum
            else:
                num += num
        if (a+b+c) > 21:
                return 'BUST'
        else:
            return (a+b+c)

</br>

##### course solution

    def blackjack(a,b,c):

        if sum((a,b,c)) <= 21:
            return sum((a,b,c))
        elif sum((a,b,c)) <=31 and 11 in (a,b,c):
            return sum((a,b,c)) - 10
        else:
            return 'BUST'

</br>

First, no need to use the for loop. All evaluations are performed using all arguments. Next, concerning the if statement, put the most straightforward evaluation first. And, finally, by using the **in** keyword, I could have checked for 11 in all arguments in one go.

</br>

#### SUMMER OF '69
Return the sum of the numbers in the array, except ignore sections of numbers starting with a 6 and extending to the next 9 (every 6 will be followed by at least one 9). Return 0 for no numbers.

**summer_69([1, 3, 5]) --> 9**</br>
**summer_69([4, 5, 6, 7, 8, 9]) --> 9**</br>
**summer_69([2, 1, 6, 9, 11]) --> 14**

This function needs to iterate thru a sequence, and fence in a range of elements with a start and stop based on stated values. It then needs to omit execution for items within that range.

##### my solution

    def summer_69(arr):                         # does not work
        total = 0
        for num in (arr):
            if num != 6:
                total = total + num
            elif num == 6:
                while num != 9:
                    pass
                else:
                    if num == 9:
                        pass
                else:
                    total = total + num
        return total

</br>

##### course solution

    def summer_69(arr):
        total = 0
        add = True
        for num in arr:
            while add:
                if num != 6:
                    total += num
                    break
                else:
                    add = False
            while not add:
                if num != 9:
                    break
                else:
                    add = True
                    break
        return total

</br>

The function needs to do something up to a point, then do nothing up to a point, then do something until it ends. In reality, it is doing something **while** a certain condition exists. And since it will transverse between these conditions, we need 2 while loops for each state.

In the course solution, the 2 while loops trigger each other by setting a variable.

</br>

### Challenge problems

#### SPY GAME
Write a function that takes in a list of integers and returns True if it contains 007 in order

**spy_game([1,2,4,0,0,7,5]) --> True**</br>
**spy_game([1,0,2,4,0,5,7]) --> True**</br>
**spy_game([1,7,2,0,4,5,0]) --> False**

This function is just like the **FIND 33** problem, it needs to identify stated consecutive values with a range.

##### my solution

    def spy_game(nums):
        for i in range(0, len(nums)-1):
            if nums[i] == 0 and nums[i+1] == 0 and nums[i+2] == 7:    # could have used index ranges here
                return True
        return False

</br>

##### course solution

    def spy_game(nums):

        code = [0,0,7,'x']

        for num in nums:
            if num == code[0]:
                code.pop(0)   # code.remove(num) also works

        return len(code) == 1

</br>

I assumed the numbers needed to be consecutive AND adjacent to one another. They only have to be consecutive, so my method using indexes would not ultimately satisfy the requirement.

The course solution simply creates a "checklist" to notch out when each number is hit. It will be consecutive this way, but not necessarily adjacent.

</br>

#### COUNT PRIMES
Write a function that returns the number of prime numbers that exist up to and including a given number

**count_primes(100) --> 25**

This function needs to create a range of every number from 2 to the argument given. Then evaluate each number for prime, and return a total of all prime numbers in that range.

##### my solution

    import math
    def count_primes(num):
        total = 0
        for n in range(2,(num)+1):
            for i in range(3, int(math.sqrt(n))+1,2):
                if n % i != 0:
                    total += 1
                else:
                    continue
        return total

    count_primes(100)         # not working
    182

</br>

##### course solution

    def count_primes(num):
        primes = [2]
        x = 3
        if num < 2:  # for the case of num = 0 or 1
            return 0
        while x <= num:
            for y in range(3,x,2):  # test all odd factors up to x-1
                if x%y == 0:
                    x += 2
                    break
            else:
                primes.append(x)
                x += 2
        print(primes)
        return len(primes)

  </br>

  - initialize the **primes** variable to collect the running total of prime numbers
  - the argument will be the top end of a range. every odd number in the range will be evaluated
  - initialize the **x** variable which will be every odd number evaluated in the range
    - the **x** will be incremented in steps of 2, so all numbers are odd.

  - the while loop runs until the top end of the range (x) is reached.
  - for every iteration, x is evaluated against factors 3 up to x for modulo 0
  - if x is prime it is appended to **primes**
  - finally all members of **primes** are printed and then the total prime numbers in the list is returned

</br>
