# TODO

The following list comprises ideas, suggestions, and known issues, all of which are in consideration for possible implementation in future releases.

***This is not a roadmap or a task list.*** Just because something is listed does not necessarily mean it will ever actually get implemented. Some might be bad ideas. Some might be impractical. Some might either not benefit enough users to justify the effort or might negatively impact too many existing users. Or I may not have the time to devote to the task.

* Rather than responding to search requests with a 404 error, allow response to be configurable:
  - 404
  - 404 with custom error message (e.g. Search has been disabled)
  - Redirect to a post or page
  - Redirect back home (but set some sort of flag that can be detected so the theme can display a message)
  - Act as if search was performed but no results were found
* Add filter to allow searching to be conditionally enabled (query obj as arg)
* Allow front-end searches for admins (and/or all logged in users?), via a Reading option and/or filters.
  If this means search widget is supported and shown, then backend handling needs to be modified to not disable the search widget, but to instead show a notice within the search widget (just within admin) saying that the widget won't actually be shown to anyone who isn't $whatever_access_criteria_is_active.

Feel free to make your own suggestions or champion for something already on the list (via the [plugin's support forum on WordPress.org](https://wordpress.org/support/plugin/disable-search/) or on [GitHub](https://github.com/coffee2code/disable-search/) as an issue or PR).
