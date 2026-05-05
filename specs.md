I'd like to start with configuration only, no programming. This project is a web app. It will run on ipads and other touch screens. I will use this project to create a gym workout. The steps will be as followed:

1. Create an account (users)
To create an account you dont need a password or an email address, just a first name. We will still have a password and email field in the user db, but they should default to something random.

2. Pick your gear (workout_items)
You will see a list of workout gear (dumbells, bench, skip rope, etc.). The user should select the gear the user has in his/her gym. This has to be saved and can be updated through a seperate menu. This is so that when a user buys more stuff he can add it. Every user should be able to pick its own gear (user_workout_item)


## The main screen
All screen should have a back button.

3. The main screen (1 - 4): User
A screen where all the users are shown

4. The main screen (2 - 4): Type
On the main screen you will see a "Create account button" (small). The rest of the buttons should fill the screen. They are:
- Start full body exersice
- Start upper body focused exercise
- Start lower body focussed exercise
- Start butt focused exersice
- ...

5. The main screen (3 - 4): Exercises
- 1
- 2
- 3
- 4
- 5
- 6
- 7
- 8
- 9
- 10


6. The main screen (4 - 4): Time and rounds per exercise
- 1 round, 60 minute
- 2 rounds, 60 minute
- 3 rounds, 60 minute
- 1 round, 45 seconds
- 2 rounds, 45 minute
- 3 rounds, 45 minute
- 1 round, 30 seconds
- 2 rounds, 30 minute
- 3 rounds, 30 minute

7. The setup page
After picking the options it should have a screen showing you how to setup each exersice. There are images for it. All exersices should show on the same page. There is a "Ready" button on this screen. When pressed it should go to the next screen.

8. The workout page
This page shows all the exercises in order. When on the screen after the ready button, it should show a count down from 3 to 1 and then GO while sounding "beep" sounds. The first exersice should be highlighted. A counter is on the screen showing the round you are in and a count down of the time left. 
On this page is also a "Pause" button, a "Restart excersice", a "Restart", a "Restart round" and "Stop workout" button. The restart button should restart the entire workout after a confirmation. The restart round should restart the current round.
There should be a 15 second space between every round.

On 10 seconds left there should be a stimulating sound bite. One should be picked randomly from an array and played.
5 seconds before the end of a round the countdown should be acompanied by a beep.

The workout should end when all rounds are done. On the end screen there should be two buttons: "Go again?" and "End workout".

## Styling
The general styling should be black background with buttons of white text. The font should be a sporty fast looking font. The styling should also be sporty and fast looking.

## Content management
All settings will be managed with commands. 
- gym:add-body-parts
- gym:add-workout-item
- gym:add-excersice; It should ask the title, which (multiple) gear it needs and which body parts (multiple) it works out
- gym; A menu to choose an option of any of the gym commands

## Tech
This is a Laravel system with Vue as frontend and a mysql db. 

## Development
- TDD is a must
- run all tests before delivering a feature
- run pint before delivering a feature
- create and update multiple end to end tests so we can ensure workings