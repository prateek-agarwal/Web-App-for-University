import { Injectable } from '@angular/core';
import { Http, Headers, Response, RequestOptions } from '@angular/http';
import { Observable }     from 'rxjs/Observable';
import 'rxjs/add/operator/map';

import 'rxjs/add/operator/toPromise';
import 'rxjs/add/operator/catch';

import { Student } from '../classes/student';

import { GatepassPreApply } from '../classes/gatepass-pre-apply';
/*
  Generated class for the GatepassService provider.

  See https://angular.io/docs/ts/latest/guide/dependency-injection.html
  for more info on providers and Angular 2 DI.
*/
@Injectable()
export class GatepassService {

  private url: string = 'http://10.0.2.2:8080';
  private body: string;
  constructor(public http: Http) {
    console.log('Hello GatepassService Provider');
  }

  checkUser (email_id: string): Observable<any> {

    let request_url = this.url + '/student';
    console.log('Email id: ', email_id);
    let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded' });
    let options = new RequestOptions({ headers: headers });

    this.body = "email_id=" + email_id;
    return this.http.put(request_url, this.body, options)
                    .map(this.extractData).
                    catch(this.handleError);
  }

  loginUser (email_id: string, password: string): Observable<any> {

    let request_url = this.url + '/getAPIKey';
    console.log('password: ', password);

    let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded' });
    let options = new RequestOptions({ headers: headers });

    this.body = "email_id=" + email_id + "&password=" + password;
    return this.http.post(request_url, this.body, options)
                   .map(this.extractData).
                   catch(this.handleError);

  }

  checkStatus(email_id: string, api_key: string): Observable<any> {
    let request_url = this.url + '/checkStatus';

    let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded', 'authorization': api_key });
    let options = new RequestOptions({ headers: headers });

    this.body = "email_id=" + email_id;
    return this.http.post(request_url, this.body, options)
                    .map(this.extractData).
                    catch(this.handleError);

  }

  getPreApply(email_id: string, api_key: string): Observable<any> {
    let request_url = this.url + '/getPreApply';

    let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded', 'authorization': api_key });
    let options = new RequestOptions({ headers: headers });

    this.body = "email_id=" + email_id;
    return this.http.post(request_url, this.body, options)
                    .map(this.extractPreApply).
                    catch(this.handleError);
  }

private extractPreApply(res: Response) {
      let body = res.json();
      console.log("Body here returned:", JSON.stringify(body));
      return body.data as GatepassPreApply || { };
    }

  private extractData(res: Response) {
      let body = res.json();
      return body.data || { };
    }

  private handleError (error: Response | any) {
    // In a real world app, we might use a remote logging infrastructure
    let errMsg: string;
    if (error instanceof Response) {

      const body = error.json() || '';
      const err = body.error || JSON.stringify(body);

      errMsg = `${error.status} - ${error.statusText || ''} ${err}`;
    } else {
      errMsg = error.message ? error.message : error.toString();
    }
    return Observable.throw(errMsg);
  }
}
