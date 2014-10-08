'use strict';

var Tm = new Marionette.Application();


Tm.Task = Backbone.Model.extend({
	defaults: {
		title: "new task",
		content: "",
		totalHours: 0,
		status: "open",
	}
});


Tm.Tasks = Backbone.Collection.extend({
	url: function () {
		return Tm.apiUrl + '/tasks';
	},
	model: Tm.Task,
});


Tm.TaskView = Marionette.ItemView.extend({
	template: '#taskView',
	events: {
		'click .close': 'close',
		'click .edit': 'edit', // same action as clicking on task name
	},
	close: function () {
		this.model.save({status: "closed"});
		Tm.tasks.remove(this.model);
	},
	edit: function () {
		Tm.router.navigate('/tasks/' + this.model.id + '/edit', {trigger: true});
	},
});

Tm.NoTasksView = Marionette.ItemView.extend({
	template: '#noTasksView',
});

Tm.TasksView = Marionette.CompositeView.extend({
	template: '#tasksView',
	childViewContainer: '.tasks',
	childView: Tm.TaskView,
	emptyView: Tm.NoTasksView,
});


Tm.TaskDetailView = Marionette.ItemView.extend({
	template: '#taskDetailView',
	events: {
		'click .close': 'close',
		'click .edit': 'edit',
	},
	templateHelpers: function () {
		return {
			contentHtml: _.escape(this.model.get('content')).replace(/\n/g, "<br\n"),
		}
	},
	close: function () {
		this.model.save({status: "closed"});
		Tm.tasks.remove(this.model);
		Tm.router.navigate('/tasks', {trigger: true});
	},
	edit: function () {
		Tm.router.navigate('/tasks/' + this.model.id + '/edit', {trigger: true});
	},
});



Tm.EditOrCreateTaskView = Marionette.ItemView.extend({
	template: '#editTaskView',
	events: {
		'click .save': 'save',
	},
	ui: {
		title: '.title',
		content: '.content',
	},
	save: function (event) {

		var self = this;

		if (!this.ui.title.val().trim()) {
			alert('Please enter task name');
			this.ui.title.focus();
			return;
		}

		Tm.tasks.add(this.model);

		this.model.save({
			title: this.ui.title.val(),
			content: this.ui.content.val(),
		}, {
			success: function () {
				Tm.router.navigate('/tasks/' + self.model.get('id'), {trigger: true});
			},
		});
	},
});


Tm.EditTaskView = Tm.EditOrCreateTaskView.extend({
	templateHelpers: function () {
		return {
			cid: this.model.id,
			pageTitle: 'Edit task',
			action: 'Save',
		};
	},
});


Tm.CreateTaskView = Tm.EditOrCreateTaskView.extend({
	templateHelpers: function () {
		return {
			cid: this.model.cid,
			pageTitle: 'Create new task',
			action: 'Create new task',
		};
	},
});


Tm.ReadmeView = Marionette.ItemView.extend({
	template: '#readmeView',
});


Tm.Router = Marionette.AppRouter.extend({
	appRoutes: {
		'': 'index',
		'tasks': 'tasks',
		'tasks/:id': 'task',
		'tasks/:id/edit': 'taskEdit',
		'create': 'create',
		'readme': 'readme',
	},
});


Tm.Controller = Marionette.Controller.extend({
	index: function () {
		Tm.router.navigate('/tasks', {trigger: true});
	},
	tasks: function () {
		Tm.content.show(new Tm.TasksView({
			collection: Tm.tasks,
		}));
	},

	_getTask: function (id) {
		var task = Tm.tasks.get(id);

		if (!task) {
			alert('task not found'); // superior error handling ftw
			Tm.router.naviget('/tasks', {trigger: true});
			throw new Error("Task not found.");
		}

		return task;
	},

	task: function (id) {
		Tm.content.show(new Tm.TaskDetailView({
			model: this._getTask(id),
		}));
	},

	taskEdit: function (id) {
		Tm.content.show(new Tm.EditTaskView({
			model: this._getTask(id),
		}));
	},

	create: function () {
		var task = new Tm.Task();

		Tm.content.show(new Tm.CreateTaskView({
			model: task,
		}));
	},

	readme: function () {
		Tm.content.show(new Tm.ReadmeView({
			model: new Backbone.Model({
				readmeHtml: Tm.readmeHtml,
			}),
		}));
	},
});


Tm.addInitializer(function (options) {
	console.log('starting app', options);
	this.apiUrl = options.apiUrl;
	this.readmeHtml = options.readmeHtml;
	this.tasks = new Tm.Tasks(options.tasks);

	this.addRegions({
		content: '#content',
	});

	this.controller = new Tm.Controller();
	this.router = new Tm.Router({
		controller: this.controller
	});

	Backbone.history.start();
});
