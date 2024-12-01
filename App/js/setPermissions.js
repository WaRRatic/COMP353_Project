function setPermissions() {
    const publicPermissions = [];
    const memberPermissions = [];
    const groupPermissions = [];

    // Collect Public Permissions
    document.querySelectorAll('#public-permissions-table tbody tr').forEach(row => {
        const permissionType = row.cells[1].querySelector('select').value;
        publicPermissions.push(permissionType);
    });

    // Collect Member Permissions
    document.querySelectorAll('#member-permissions-table tbody tr').forEach(row => {
        const authorizedMember = row.cells[1].querySelector('input').value;
        const memberPermissionType = row.cells[2].querySelector('select').value;
        memberPermissions.push({
            member_id: authorizedMember,
            permission: memberPermissionType
        });
    });

    // Collect Group Permissions
    document.querySelectorAll('#group-permissions-table tbody tr').forEach(row => {
        const authorizedGroup = row.cells[1].querySelector('input').value;
        const groupPermissionType = row.cells[2].querySelector('select').value;
        groupPermissions.push({
            group_id: authorizedGroup,
            permission: groupPermissionType
        });
    });

    // Assign the serialized data to hidden fields
    document.getElementById('publicPermissions').value = JSON.stringify(publicPermissions);
    document.getElementById('memberPermissions').value = JSON.stringify(memberPermissions);
    document.getElementById('groupPermissions').value = JSON.stringify(groupPermissions);

    return true; // Allow form submission to proceed
}