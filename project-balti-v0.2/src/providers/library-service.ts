import { Injectable } from '@angular/core';
import { Http, Headers, Response, RequestOptions } from '@angular/http';
import { Observable }     from 'rxjs/Observable';
import 'rxjs/add/operator/map';

import 'rxjs/add/operator/toPromise';
import 'rxjs/add/operator/catch';

import { Student } from '../classes/student';

import { Book } from '../classes/library-service';
/*
/*
  Generated class for the LibraryService provider.

  See https://angular.io/docs/ts/latest/guide/dependency-injection.html
  for more info on providers and Angular 2 DI.
*/
@Injectable()
export class LibraryService {

  private url: string = 'http://10.0.2.2:8080';
  private body: string;

  constructor(public http: Http) {
    console.log('Hello LibraryService Provider');
  }
  getIssuedBookDetails(enrollment_no:string): Observable<any>{

    let request_url = this.url + '/getIssuedBookDetails';
    console.log('Enrollment Number: ', enrollment_no);
    let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded' });
    let options = new RequestOptions({ headers: headers });

    this.body = "Enrollment_Number=" + enrollment_no;
    return this.http.get(request_url)
                    .map(this.extractData)
                    catch(this.handleError);
  }
  getFine(enrollment_no: string): Observable<any>{

    let request_url = this.url + '/getFine';
    console.log('Enrollment Number: ', enrollment_no);
    let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded' });
    let options = new RequestOptions({ headers: headers });
    this.body = "Enrollment_Number=" + enrollment_no;
    return this.http.get(request_url)
                    .map(this.extractData)
                    catch(this.handleError); 
  }
  getBook(keyword: string): Observable<any>{

    let request_url = this.url + '/getBook';
    console.log('Book Searched with keyword as: ', keyword);
    let headers = new Headers({ 'Content-Type': 'application/x-www-form-urlencoded' });
    let options = new RequestOptions({ headers: headers });
    this.body = "Book Searched with keyword as=" + keyword;
    return this.http.get(request_url)
                    .map(this.extractData)
                    catch(this.handleError); 
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
