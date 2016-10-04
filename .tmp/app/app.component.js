var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
import { Component, ViewChild } from '@angular/core';
import { Platform, MenuController, Nav } from 'ionic-angular';
import { StatusBar } from 'ionic-native';
import { HomePage } from '../pages/home/home';
import { GatepassPage } from '../pages/gatepass-page/gatepass-page';
import { TimetablePage } from '../pages/timetable-page/timetable-page';
import { LibraryPage } from '../pages/library-page/library-page';
export var MyApp = (function () {
    function MyApp(platform, menu) {
        this.platform = platform;
        this.menu = menu;
        this.rootPage = HomePage;
        this.initializeApp();
        this.pages = [
            { title: 'Home', component: HomePage },
            { title: 'Gatepass', component: GatepassPage },
            { title: 'Timetable', component: TimetablePage },
            { title: 'Library', component: LibraryPage }
        ];
    }
    MyApp.prototype.initializeApp = function () {
        this.platform.ready().then(function () {
            // Okay, so the platform is ready and our plugins are available.
            // Here you can do any higher level native things you might need.
            StatusBar.styleDefault();
        });
    };
    MyApp.prototype.openPage = function (page) {
        // close the menu when clicking a link from the menu
        this.menu.close();
        // navigate to the new page if it is not the current page
        this.nav.setRoot(page.component);
    };
    __decorate([
        ViewChild(Nav), 
        __metadata('design:type', Nav)
    ], MyApp.prototype, "nav", void 0);
    MyApp = __decorate([
        Component({
            templateUrl: 'app.html'
        }), 
        __metadata('design:paramtypes', [Platform, MenuController])
    ], MyApp);
    return MyApp;
}());
