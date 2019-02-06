    import {PostStatus} from "./PostStatus";

export default class  {
    constructor(public id, public method: string, public url: string, public timeExecute: Date, public body: string, public user: number, public status: PostStatus){}
}