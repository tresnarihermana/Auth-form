import {usePage} from '@inertiajs/vue3';
export function can(permission: string) : boolean{
    const page = usePage();
    const permissions: string[] = page.props.auth.permissions ?? [];
    return permissions.includes(permission);

}