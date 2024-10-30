<x-admin-layout>
    <div class="flex flex-col">
        @can('Create permissions')
                <div class="flex justify-end pb-3">
                    <a style="background-color: green; transition: background-color 0.3s;"
                    onmouseover="this.style.backgroundColor='#28a745'"
                    onmouseout="this.style.backgroundColor='green'" href="{{route('admin.permissions.create')}}" class="px-4 py-2 text-white rounded-md">Create Permission</a>
                </div>
                @endcan
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                
                <div>
                    <table id="example" class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Permissions</th>
                                <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase">Roles</th>
                                @canany(['Edit permissions', 'Delete permissions'])
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                                @endcanany
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($permissions as $permission)
                                <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">{{$permission->name}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                                    @foreach ($permission->roles as $role)
                                    <span class="bg-blue-100 text-blue-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center items-center">
                                    @can('Edit permissions')
                                    <a href="{{route('admin.permissions.edit', $permission->id)}}" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.304 4.844 2.852 2.852M7 7H4a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-4.5m2.409-9.91a2.017 2.017 0 0 1 0 2.853l-6.844 6.844L8 14l.713-3.565 6.844-6.844a2.015 2.015 0 0 1 2.852 0Z"/>
                                        </svg>
                                        <span class="sr-only">Edit</span>
                                    </a>
                                    @endcan
                                    @can('Delete permissions')
                                    <form method="POST" action="{{route('admin.permissions.destroy', $permission->id)}}" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')    
                                        <button type="submit" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-full text-sm p-2.5 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"/>
                                            </svg>
                                            <span class="sr-only">Delete</span>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
