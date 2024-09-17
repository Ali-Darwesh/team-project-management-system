
# Team Project Management System

I create this project to make the management of my projects easiest.
I can create projects and add users to it with differente roles :
- Manager to make tasks for the project
- Developer to do tasks
- Tester to test the tasks after it completed

thay can get projects with users work on it and the contribution_hours for each user 
when user start work on project he must press on start button to get the time of start work
 and when he finish press on end then the contribution_hours counted 

## Pastman
- postman/endPointTask6.postman_collection.json
## create the project
- composer create-project --prefer-dist laravel/laravel team-project-management-system "10.*"
- update env file to init database conniction
- php artisan migration --seed
 admin accout will be created

 ## RelationShips
 I use:
 - many-to-many relation between projects and users
 - hasMany between projects and tasks
 - HasManyThrow between users and tasks

 ## Filter the data
  I make filter on status and priority using WhereRelation
  I get latest task using latestOfMany 
  I get oldest task using oldestOfMany 
  I get the most priority task using ofMany

## github

(https://github.com/Ali-Darwesh/team-project-management-system)
