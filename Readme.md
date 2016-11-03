It is project to learn Angular and to create convenient to-do list.

The list includes daily, weekly, normal tasks.
Also it allows to create revolver tasks (tasks including ordered sub-tasks).

=============== TO DO ================


- add bonus prize system, earning coins:
Did all everyday tasks for one day? Earn a silver coin.
Did all everyday tasks for a whole week? Earn a gold coin.
Did all everyday tasks for a whole month? Earn a platinum coin.
Did a plain task? Earn copper coin.
Did one kind of a tasks for a whole week? Earn a copper coin.
Etc.

- dynamically change legend in dependence from language selected

- save language preference to cookies

- force completed normal items to be disappear eventually next days after completion:
make font smaller and thinner.


- translate current date month to Russian
- fix Russian {1 день, 2-3-4 дня, 5-6-7 дней} translation

- allow to set tasks for particular date.
- add weekly and monthly tasks;

- create FlashService to indicate that login was successful or not. 
Download working sample to check how Johnpapa did it.

- allow Google Accounts login?

- add graphs. Like weight site.
- add calendar to set required date;

- allow items drag & n & drop;
- add amount of completed points per day, progress bar.
- add revolver tasks;

- allow hide & display panels;

- allow setting statistic range: calendar months/weeks or any other defined time interval.

- on edit click - focus edit field, select it's content. On 'escape' click close it without save.



=============== DONE ====================

- fix "cannot remove" bug;

- set legend fit current locale

- fix wrong delay calculation: if complete task 1 day ago then complete it 2 days ago it shows as completed 2 days ago

- translate "never did", "X days ago"

- fix statistic:  ∞% ∞% ∞%  in final percentage for single completed item

- internationalize it (add Russian templates and transcripts)
- add translations, in particular to Russian.
- deploy it
- properly intercept "not logged" exception
- set Login/Logout button depend on if user is logged in.
- on security exception redirect user to login page.

- add authentication system. Security.
- move active button (Main/Statistic/Archive)
- add history (access to archive) for 'normal' tasks;
- allow to recover or to irreversibly delete archived tasks;

/*
 * - add statistics. Separate page.:
 * 
 * monthly & weekly completion % of every daily task;
 * monthly & weekly completion % of all daily/normal tasks;
 */

