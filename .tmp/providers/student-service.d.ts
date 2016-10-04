import { Http } from '@angular/http';
import 'rxjs/add/operator/map';
export declare class StudentService {
    http: Http;
    data: any;
    constructor(http: Http);
    load(userid: String): Promise<{}>;
}
