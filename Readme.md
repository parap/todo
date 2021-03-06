Convenient to-do list of daily tasks.
Statistics of the tasks done.

Deployed at http://dom4logs.com/todo

The list includes daily, weekly, normal tasks.
Also it allows to create revolver tasks (tasks including ordered sub-tasks).

=========== TODO now ============

- show monthly and weekly tasks that are uncompleted 
if they were not completed last time.

- add tags (hypotheses tested, books read), let setup tags for main page.

- add button "archive completed" to regular tasks

- by default display approaching weekly&monthly tasks. For instance in closest 2 days.

- add annotation when creating item. Display annotation 
as title (standard HTML popup) on item hover

- let user to add comments to item.

- on edit regular field - hide it's time mark

- add titles to all managing elements of the site to make them easier to understand

=============== FIX BUGS =================

- dynamically change legend in dependence from language selected

- fix pluralization for "day" in Russian

- save language preference to cookies

- when adding new subitem, move focus there

- fix bug for 12th month statistic? month's % can't be 0 while week's % > 0

=============== TODO refactor ==============

- migrate to Doctrine;

- use Postgres to train it;

- refactor. remove completed_at field from item table.
Thus any type of task will be processed similarly.

- refactor: move "edit daily task" forms to separate template and processing code to separate controller
to make todo-list template and component simpler

=============== TODO cosmetics and User Interface =============

- separate plain tasks and completed tasks. 
Move completed tasks with completion period > 2 days to "completed" tab

- translate the rest of site current date month to Russian.

- allow Google Accounts login.



- add to login page auto-link to test account;

- add description of the project page;

- show RIP images for plain tasks outdated for a week, month, every next month, year

- add tags to be able to fetch tasks of some tag only;

- allow items drag & n & drop;

- add amount of completed points per day, progress bar.

- allow hide & display panels;

- on edit click - focus edit field, select it's content. On 'escape' click close it without save.

- force completed normal items to be disappear eventually next days after completion:
make font smaller and thinner.

- create FlashService to indicate that login was successful or not. 
Download working sample to check how Johnpapa did it.

- add calendar button to select date to "Next day" and "Previous day" buttons

- allow deletion of weekly&monthly tasks in archive. Display them in archive.

- add comments to tasks to add on-fly. Like Trello has.

=============== TODO new functionalities ===========

- make advertisement campaign to attract users

- add yearly tasks.

- add scores, values for daily tasks. So there could be more and less important tasks.

- add graphs. Like weight site.

- add bonus prize system, earning coins:
Did all everyday tasks for one day? Earn a silver coin.
Did all everyday tasks for a whole week? Earn a gold coin.
Did all everyday tasks for a whole month? Earn a platinum coin.
Did a plain task? Earn copper coin.
Did one kind of a tasks for a whole week? Earn a copper coin.
Etc.

- let user PAUSE daily tasks

- let user to mark tasks as NOT DONE. Thus we can distinct unfilled tasks from UNDONE tasks.
let user have some minor points for such marking.

- add buttons to calendar 'in a week', 'in a month'

- add tags

=============== TODO statistic ===========

- allow setting statistic range: calendar months/weeks or any other defined time interval.

- sort items according to month statistic (move rarely completed-popular up, often completed-popular down)

=============== DONE ====================

- setup Google Analytics

- add 'close' button for 'edit item' field

- add placeholders for subitem fields

- add "weekly tasks only" mode - display all weekly tasks

- add "monthly tasks only" mode - display all monthly tasks

- fix: when removing last subtask - it doesn't save

- fix: when saving item with several subtasks - subtasks shuffle. 1-2-3 => 3-1-2 => 2-3-1 => 1-2-3

- FIX fucking login let user to stay online for a long! Cookie disappears for some mysterious reason

- !!! BASE: add revolver tasks;

- make user to be logged in for 1 day (not a few hours as it is now). Even for months.

- !!! BASE: add weekly and monthly tasks;

- refactor: rename "daily" table to "completed" table

- fix "logged in" state of menu buttons for logged out user due to outdated session

- fix Russian {1 день, 2-3-4 дня, 5-6-7 дней} translation
- make completed dates to be shown in green
- allow selecting dates in the past in calendar

- color calendar dates in dependence from how much time left

- add calendar to set required date;

- allow to set tasks for particular date.

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

- add statistics. Separate page.:
- monthly & weekly completion % of every daily task;
- monthly & weekly completion % of all daily/normal tasks;
