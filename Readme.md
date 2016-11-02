It is project to learn Angular and to create convenient to-do list.

The list includes daily, weekly, normal tasks.
Also it allows to create revolver tasks (tasks including ordered sub-tasks).

=============== TO DO ================

- save language preference to cookies

- force completed normal items to be disappear eventually next days after completion:
make font smaller and thinner.

- set legend fit current locale

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


- on edit click - focus edit field, select it's content. On 'escape' click close it without save.

=============== DONE ====================

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

