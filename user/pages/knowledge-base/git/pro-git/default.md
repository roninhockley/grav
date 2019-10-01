---
visible: true
title: How to use GIT
author:
  name: Ron Fish
taxonomy:
  category: KB
  tag: [KB,gitlab,git]
---
[toc]

## Background

The three main sections of a Git project: the Git directory, the
working directory, and the staging area:

![](./3sections.png)

The Git directory `.git` is where Git stores the metadata and object database for
your project. This is the most important part of Git, and it is what is copied
when you clone a repository from another computer.

The working directory is a single checkout of one version of the project.
These files are pulled out of the compressed database in the Git directory and
placed on disk for you to use or modify.

The staging area is a file, `.git/index` generally contained in your Git directory, that
stores information about what will go into your next commit. It’s sometimes re-
ferred to as the “index”, but it’s also common to refer to it as the staging area.

The basic Git workflow goes something like this:

1. You modify files in your working directory.
2. You stage the files, adding snapshots of them to your staging area.
3. You do a commit, which takes the files as they are in the staging area and
stores that snapshot permanently to your Git directory.

If a particular version of a file is in the Git directory, it’s considered commit-
ted. If it has been modified and was added to the staging area, it is staged. And
if it was changed since it was checked out but has not been staged, it is modi-
fied.

## Basics on usage

To initialize git in an existing project directory do `git init`. This creates the **.git** folder which is your repository skeleton. The actual repo sits in here.  

To start tracking the files in your project do:
    git add .  # or file/wildcard/directory etc
    git commit -m 'initial commit'

To pull down a remote repo do `git clone <URL>`.

For a new repo, going forward, any files that are either staged (git add) or have been committed will now be tracked files, because they will now be in either the index (staged) snapshot or in the `.git` folder (committed). Any files that have been added to the working tree but have not been committed and are not currently staged, will be untracked files.

To summarize, the 3 main areas of a repo:

- **.git folder** - anything in here is a tracked file
- **.git/index** - anything in here is staged. the staging area is a snapshot, so these files are tracked. (unless you unstage them)
- **working tree** - any files in here that have not been previously committed, and are not currectly staged are untracked.

When you clone a repo, all files that were pulled down during the clone are tracked, since they are already in the snapshot under `.git`. As above, any files in the repo that were not in the original snapshot when cloned, and have not been committed, and are not currently staged, will be untracked files.

Tracked files can be in one of 3 states:  *unmodified, modified,* or *staged*.
- **unmodified** - they are in the working tree in the same state (checksum) as the .git snapshot.
- **modified** - they are in a different state in the working tree than in the .git snapshot
- **staged** - they are currently in the **.git/index** staging file, to be committed.

![](./tracked-states.png)

### git ignore

Consult the Pro Git book which has a nice explanation of the syntax for the `.gitignore` file.

### Remove files

Use `git rm` to remove files. From there you commit, then the file is gone from the repo and from your working directory. If you do `git rm --cached` it will NOT be removed from the working directory. 

### Move files

To rename a file use `git mv`. Git will rm the old filename and add the new one.

### Viewing history log

Use `git log` to see past commit history. Some variations include:
- **git log -s** - short log view
- **git log -p** - more detailed view that shows the actual diff changes
- **git log -p -2** - show details, but only show 2 log records. The last 2 commits in other words.

### Amending a commit

Use `git commit --amend` if you made a commit but want to either modify the commit message or add more files. If you run the amend and you have not made any additional changes to the files committed previously it will only bring up the commit editor for the last commit so you can modify it.

If you make a commit, then you realize you wanted to add more changes to that commit, or you want more changes to be on that last commit, you can make the additional changes, then do `git add` followed by `git commit --amend` and the additional changes will be added to the last commit.

### Unstaging files

To remove a file from the staging area use `git reset HEAD <filename>`. Afterwards, the file will remain modified in the working directory, but not be staged for commit.

### Undoing modifications to files in working directory

Use `git checkout -- <filename>` to undo changes to a file in the working directory. GIT will simply revert the file to its state during the last commit. In other words it will pull it from the .git directory (snapshot).

!!! This is irreversible. So be sure you can live without those changes before doing this.

### Working with remotes

A remote is simply a version of your project that is hosted on another computer. 

### Show remotes

Use `git remote -v` to display URLs for all remotes.

### Adding remotes

To add a remote for a repo, use `git remote add <shortname> <URL>`. Then you can use `git fetch <shortname>` to fetch the remote files.

### Fetching and pulling from remotes

As mentioned above, use `git fetch <shortname>` to pull down all data from a remote that you dont already have. 
When you clone a repo, a remote named **origin** will automatically be added to the list of remotes.

Note that `git fetch` does not merge the remote version of any file into your current work. You have to do that manually. The `git pull` command fetches and merges automatically, and is usually the more comfortable workflow. When you run `git pull` and the remote version of a file you have locally is different, a merge commit will be generated and the editor will pop up for you to enter a commit message.

If after running `git pull` the remote version of a file will have changes that conflict with changes you made (and committed), a merge conflict will occur which has to be dealt with before the remote can be merged.

### Pushing to remotes

To push your committed changes to a remote use `git push <remote name> <branch name>`. So to push your local master branch to the origin server, use `git push origin master`.

### Inspecting a remote

To see more info about a remote use `git remote show <remote name>`. 

### Removing and renaming remotes

To change a remote's shortname, use `git remote rename <current shortname> <new shortname>`. This also changes it on the remote server. In other words if you do `git remote rename rf ron`, the reference to *rf/master* will now be *ron/master*.

To remove a remote use `git remote rm <shortname>`.

### Tagging

Tagging is used to mark important commits in the history. It is typically used to tag a release point like **v1.0** or **v1.1** etc.

### Listing tags

Use `git tag` to list all tags in the repo. They will be listed alphabetically.
You can also search for tags with a pattern, like `git tag -l "v1.8*"`. 

### Creating tags

There are 2 main types of tags, **lightweight** and **annotated**.

A lightweight tag is just a pointer to a specific commit. It is like a branch that never changes.

An annotated tag has much more information, and is stored in GIT as a full object. It is checksummed, has the tagger's name, email, date, a tagging message, and can be signed with GPG.

It is recommended to create annotated tags unless the tag is temporary or not important enough to have all this info.

### Annotated tags

To create an annotated tag, just do `git tag -a <tagname> -m "tagging message"`, for example `git tag -a v1.3 -m "version 1.3"`. This applies to the last commit. To tag a specific commit add some of the commit hash to the end.

### Lightweight tags

A lightweight tag is basically just a commit's checksum stored in a file, no other info is kept. 
To create a lightweight tag, just do not specify the *-a, -s,* or *-m* options. `git tag v1.3`.
As mentioned above, this tags the latest commit. To tag a specific commit, add the hash to the end.

### Sharing tags

By default `git push` does not push your tags upstream. To push a tag upstream do `git push <remote name> <tagname>`. Or to push all your tags at once do `git push <remote name> --tags`. 

### Git aliases

Just like aliases in bash, you can create shortcuts with git commands.
`git config --global alias.co checkout` will create an alias that allows you to type **co** instead of **checkout**.

Even better you could create a better alias for unstaging a file:

`git config --global alias.unstage 'reset HEAD --'`. Then, instead of typing `git reset HEAD -- <file>` you can do `git unstage file` to accomplish the same thing.

## Branching

