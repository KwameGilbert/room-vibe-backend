#Intelligence Checker
# 0 -5 : Dumb
# 6- 10 : Smart
# 11 - 20 : Clever
#21 - 30 : Brilliant

# Display 'Enter your age'
print('Enter your age...')
# Accept user age
age = int(input())

if(age >= 0 and age <= 5):
    print('You are dumb')
elif(age >= 6 and age <= 10):
    print('You are smart')
elif(age >= 11 and age <= 20):
    print('You are clever')
else:
    print('You are brilliant')

