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
		'click .close': 'closeTask',
		'click .edit': 'edit',
		'click .track': 'track',
	},
	modelEvents: {
		change: 'render',
	},
	closeTask: function () {
		this.model.save({status: "closed"});
		Tm.tasks.remove(this.model);
	},
	edit: function () {
		Tm.router.navigate('/tasks/' + this.model.id + '/edit', {trigger: true});
	},
	track: function () {
		Tm.router.navigate('/tasks/' + this.model.id + '/track', {trigger: true});
	},
});


Tm.NoTasksView = Marionette.ItemView.extend({
	template: '#noTasksView',
});


Tm.TasksView = Marionette.CompositeView.extend({
	template: '#tasksView',
	childViewContainer: '.taskList',
	childView: Tm.TaskView,
	emptyView: Tm.NoTasksView,
	onRender: function () {
		// always try to load new tasks from server
		this.collection.fetch();
	}
});


Tm.TaskTimeEmptyView = Marionette.ItemView.extend({
	template: '#taskTimeEmptyView',
	tagName: 'tr',
});


Tm.TaskTimeView = Marionette.ItemView.extend({
	tagName: 'tr',
	template: '#taskTimeView',
});


Tm.TaskDetailView = Marionette.CompositeView.extend({
	template: '#taskDetailView',
	events: {
		'click .close': 'closeTask',
		'click .edit': 'edit',
		'click .track': 'track',
	},
	templateHelpers: function () {
		return {
			contentHtml: _.escape(this.model.get('content')).replace(/\n/g, "<br\n"),
		}
	},
	closeTask: function () {
		this.model.save({status: "closed"});
		Tm.tasks.remove(this.model);
		Tm.router.navigate('/tasks', {trigger: true});
	},
	edit: function () {
		Tm.router.navigate('/tasks/' + this.model.id + '/edit', {trigger: true});
	},
	track: function () {
		Tm.router.navigate('/tasks/' + this.model.id + '/track', {trigger: true});
	},
	initialize: function () {
		this.collection = new (Backbone.Collection.extend({
			url: this.model.url() + '/time',
		}))();
	},
	childViewContainer: '.time',
	childView: Tm.TaskTimeView,
	emptyView: Tm.TaskTimeEmptyView,
	onRender: function () {
		this.collection.fetch();
	},
	collectionEvents: {
		'sync': 'timeLoaded',
	},
	timeLoaded: function () {
		this.$('.loadingInfo').hide();
		this.$('.timeTable').removeClass('loading');
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
			pageTitle: 'Edit task',
			action: 'Save',
		};
	},
});


Tm.CreateTaskView = Tm.EditOrCreateTaskView.extend({
	templateHelpers: function () {
		return {
			pageTitle: 'Create new task',
			action: 'Create new task',
		};
	},
});


Tm.TaskTrackView = Marionette.ItemView.extend({
	template: '#taskTrackView',
	templateHelpers: function () {
		return {
			date: (new Date()).toDateString(),
		}
	},
	events: {
		'click .track': 'track',
	},
	track: function () {
		var self = this;
		$.ajax({
			url: this.model.url() + '/time',
			type: 'post',
			contentType: 'application/json; charset=utf8',
			data: JSON.stringify({
				date: this.$('.date').val(),
				hours: parseInt(this.$('.hours').val(), 10),
			}),
			success: function (data) {
				// task data are returned back with computed totalHours
				self.model.set(data);
				Tm.router.navigate('/tasks/' + self.model.id, {trigger: true});
			},
			error: function () {
				alert('Request failed.');
			},
		});
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
		'tasks/:id/track': 'taskTrack',
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

	taskTrack: function (id) {
		Tm.content.show(new Tm.TaskTrackView({
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
