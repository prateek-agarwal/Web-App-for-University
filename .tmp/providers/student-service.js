var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
import { Injectable } from '@angular/core';
import { Http } from '@angular/http';
import 'rxjs/add/operator/map';
/*
  Generated class for the StudentService provider.

  See https://angular.io/docs/ts/latest/guide/dependency-injection.html
  for more info on providers and Angular 2 DI.
*/
export var StudentService = (function () {
    function StudentService(http) {
        this.http = http;
        console.log('Hello StudentService Provider');
    }
    StudentService.prototype.load = function (userid) {
        var _this = this;
        /*if (this.data) {
          // already loaded data
          return Promise.resolve(this.data);
        }
        */
        // var headers = new Headers();
        //    headers.append('Content-Type', 'application/x-www-form-urlencoded');
        // var body = 'user_id=U101114FCS223';
        // don't have the data yet
        var url = "http://localhost:8080/viewStudent?user_id=" + userid.toString();
        return new Promise(function (resolve) {
            // We're using Angular HTTP provider to request the data,
            // then on the response, it'll map the JSON data to a parsed JS object.
            // Next, we process the data and resolve the promise with the new data.
            _this.http.get(url)
                .map(function (res) { return res.json(); })
                .subscribe(function (data) {
                // we've got back the raw data, now generate the core schedule data
                // and save the data for later reference
                _this.data = data;
                resolve(_this.data);
            });
        });
    };
    StudentService = __decorate([
        Injectable(), 
        __metadata('design:paramtypes', [Http])
    ], StudentService);
    return StudentService;
}());
