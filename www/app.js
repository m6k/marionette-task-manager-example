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
	},
	close: function () {
		this.model.save({status: "closed"});
	},
});

Tm.TasksView = Marionette.CompositeView.extend({
	template: '#tasksView',
	childViewContainer: '.tasks',
	childView: Tm.TaskView,
});

Tm.EditTaskView = Marionette.ItemView.extend({
	template: '#editTaskView',
	events: {
		'click .save': 'save',
	},
	ui: {
		title: '.title',
		content: '.content',
	},
	save: function (event) {

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
				Tm.router.navigate('/tasks', {trigger: true});
			},
		});
	},
});

Tm.CreateTaskView = Tm.EditTaskView.extend({
	templateHelpers: function () {
		return {
			cid: this.model.cid,
			action: 'Create new task',
		};
	},
});


Tm.Router = Marionette.AppRouter.extend({
	appRoutes: {
		'': 'index',
		'tasks': 'tasks',
		'create': 'create',
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

	create: function () {
		console.log('create');
		var task = new Tm.Task();

		Tm.content.show(new Tm.CreateTaskView({
			model: task,
		}));
	},
});

Tm.addInitializer(function (options) {
	console.log('starting app', options);
	this.apiUrl = options.apiUrl;
	this.tasks = new Tm.Tasks(options.tasks);

	this.addRegions({
		content: '#content',
	});

	this.controller = new Tm.Controller();
	this.router = new Tm.Router({
		controller: this.controller
	});

	Backbone.history.start({pushState: true, root: "/"});
});
