# Web page for Majority judgment (SYMFONY) 

All this project is in French only for the moment.

This project use this package for MajorityJudgement : [Majority Judgement PHP](https://github.com/oceanBigOne/MajorityJudgment)
See more details on majority jugement here : [Wikipedia](https://en.wikipedia.org/wiki/Majority_judgment).



# How to install ?

- Deploy project on your webserver with git (or FTP width ``develop branch`` for the moment!).

With git:
```
git clone https://github.com/oceanBigOne/jm.git .
``` 

- Reset project on develop branch: 

```
git reser --hard origin/develop
```

- Create ``.env``  file with ``.env.dist`` model 

  Change access to your database in this line:
  ```
  DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
  ```
  Change SALT_KEY with any string you want (!important for "hiding" link of vote)
  ```
  SALT_KEY=XXXXXXXXXXXXXXXXXXXXXXX;
  ```
  And set APP_ENV to "prod"
  ```
  APP_ENV=prod
  ```
  
- Then deploy BDD with:
```
php bin/console doctrine:migrations:migrate
```

- Create ``public/.htaccess`` with ``public/.htaccess.sample`` (check if config of your sever require somes changes)

- And clear symfony cache:
```
php bin/console cache:clear --no-warmup -e prod
```
- set cron with WGET to clear old data and generate result cache
```
* */6 * * * wget -q --delete-after [URL]/clear
* */1 * * * wget -q --delete-after [URL]/generate-results
``` 

That's all...

## Versions

#### 2.2.5
- add banner and link to new app 

#### 2.2.4
- bug fix : viewing result admin page can cache result data before the end of vote

#### 2.2.3
- bug fix : counter of participations on result page

#### 2.2.2
- security fix
- format vote card (bold <b>, Italic <i>, link and line break)

#### 2.2.1
- update npm dependencies

### 2.2.0
- Strip poll
- Cached result (fix a performance problem)
- Add Mieux Voter Logo
- Shuffle array of choices on vote form
- Use abstractController interface instead of deprecated Controller Interface
- update bootstrap

#### 2.1.3
- tar update

#### 2.1.2
- Jquery update
- popper.js update

#### 2.1.1
- English version (fixed translation)

### 2.1.0
- English version

#### 2.0.3
- Remove links with Google Font and Youtube

#### 2.0.2
- Start vote form : The limit of the number of candidates is now 100

#### 2.0.1
- Result page : display the exact percentage instead of 50% in the explanation sentence

### 2.0.0
- Add Advanced option : Admin link, limit max users, send links by mail, Facebook sharing, asking name of users 

#### 1.7.4
- Bad default value on vote form

#### 1.7.3
- Margin fix on header picture

#### 1.7.2
- opportunity to vote a few minutes

#### 1.7.1
- fix home : width of video (responsive problem)

### 1.7.0
- Design and ergonomics

#### 1.6.1
- Composer Update (update version of MajorityJudgement package)

### 1.6.0
- Composer Update (update version of MajorityJudgement package)

### 1.5.0
- Use index to compare ex-eaquo

### 1.4.0
- Add index on result page

### 1.3.0
- Use MajorityJudgement 2.X instead of 1.X

#### 1.2.2
- hotfix : Barchart height
- hotfix : maxlength to 150 chars for label choice

#### 1.2.1
- hotfix : reverse label on Barchart

### 1.2.0
- update Barchart

#### 1.1.5
- Create .htaccess.sample

#### 1.1.4
- update htaccess : "force" HTTPS

#### 1.1.3
- License

#### 1.1.2
- fix version in readme 

#### 1.1.1
- spelling errors

### 1.1.0
- User interface update

### 1.0.0
- Initial version





