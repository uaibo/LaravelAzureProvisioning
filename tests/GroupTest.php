<?php

namespace RobTrehy\LaravelAzureProvisioning\Tests;

class GroupTest extends TestCase
{
    public function testCanGetGroups()
    {
        $response = $this->get("/$this->routePrefix/Groups");

        $response->assertStatus(200);
        $response->assertJsonFragment(["totalResults" => 0]);
    }

    public function testCanPostGroupWithNoMembers()
    {
        $response = $this->post("/$this->routePrefix/Groups", [
            "externalId" => uniqid(),
            "DisplayName" => "BobsAmazingGroup",
            "members" => [],
            "schemas" => [
                "urn:ietf:params:scim:schemas:core:2.0:Group"
            ]
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "displayname" => "BobsAmazingGroup",
        ]);
    }

    public function testCanPostGroupWithMembers()
    {
        // Post a User first
        $this->post("/$this->routePrefix/Users", [
            "UserName" => "UserName123",
            "Active" => true,
            "DisplayName" => "BobIsAmazing",
            "schemas" => [
                "urn:ietf:params:scim:schemas:core:2.0:User"
            ],
            "externalId" => uniqid(),
            "emails" => [
                [
                    "Primary" => true,
                    "type" => "work",
                    "value" => "testing@bob.com"
                ]
            ]
        ]);

        $response = $this->post("/$this->routePrefix/Groups", [
            "externalId" => uniqid(),
            "DisplayName" => "BobsAmazingGroup Now with Members!",
            "members" => [
                [
                    "value" => 1
                ]
            ],
            "schemas" => [
                "urn:ietf:params:scim:schemas:core:2.0:Group"
            ]
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "displayname" => "BobsAmazingGroup Now with Members!"
        ]);
        $response->assertJsonFragment([
            "username" => "UserName123",
            "displayname" => "BobIsAmazing",
        ]);
    }

    public function testCanPatchGroup()
    {
        // Post a User first
        $this->post("/$this->routePrefix/Users", [
            "UserName" => "UserName123",
            "Active" => true,
            "DisplayName" => "BobIsAmazing",
            "schemas" => [
                "urn:ietf:params:scim:schemas:core:2.0:User"
            ],
            "externalId" => uniqid(),
            "emails" => [
                [
                    "Primary" => true,
                    "type" => "work",
                    "value" => "testing@bob.com"
                ]
            ]
        ]);

        // Now Post an empty Group
        $this->post("/$this->routePrefix/Groups", [
            "externalId" => uniqid(),
            "DisplayName" => "BobsAmazingGroup",
            "members" => [],
            "schemas" => [
                "urn:ietf:params:scim:schemas:core:2.0:Group"
            ]
        ]);

        // Patch the User to the Group
        $response = $this->patch("/$this->routePrefix/Groups/1", [
            "schemas" => [
                "urn:ietf:params:scim:api:messages:2.0:PatchOp"
            ],
            "Operations" => [
                [
                    "name" => "addMember",
                    "op" => "add",
                    "path" => "members",
                    "value" => [
                        [
                            "value" => 1
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "displayname" => "BobsAmazingGroup"
        ]);
        $response->assertJsonFragment([
            "username" => "UserName123",
            "displayname" => "BobIsAmazing",
        ]);
    }

    public function testCanPutGroup()
    {
        // Post a User first
        $this->post("/$this->routePrefix/Users", [
            "UserName" => "UserName123",
            "Active" => true,
            "DisplayName" => "BobIsAmazing",
            "schemas" => [
                "urn:ietf:params:scim:schemas:core:2.0:User"
            ],
            "externalId" => uniqid(),
            "emails" => [
                [
                    "Primary" => true,
                    "type" => "work",
                    "value" => "testing@bob.com"
                ]
            ]
        ]);

        // Then Post an empty Group
        $this->post("/$this->routePrefix/Groups", [
            "externalId" => uniqid(),
            "DisplayName" => "BobsAmazingGroup",
            "members" => [],
            "schemas" => [
                "urn:ietf:params:scim:schemas:core:2.0:Group"
            ]
        ]);

        $response = $this->put("/$this->routePrefix/Groups/1", [
            "DisplayName" => "Bob had an amazing group",
            "members" => [
                [
                    "value" => 1
                ]
            ],
            "schemas" => [
                "urn:ietf:params:scim:schemas:extension:enterprise:2.0:User",
                "urn:ietf:params:scim:schemas:core:2.0:User"
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "displayname" => "Bob had an amazing group"
        ]);
        $response->assertJsonFragment([
            "username" => "UserName123",
            "displayname" => "BobIsAmazing",
        ]);
    }

    public function testCanDeleteGroup()
    {
        // Post a Group First
        $this->post("/$this->routePrefix/Groups", [
            "externalId" => uniqid(),
            "DisplayName" => "BobsAmazingGroup",
            "members" => [],
            "schemas" => [
                "urn:ietf:params:scim:schemas:core:2.0:Group"
            ]
        ]);

        $response = $this->delete("/$this->routePrefix/Groups/1");

        $response->assertNoContent($status = 204);

        // Now verify the Group has gone!
        $response = $this->get("/$this->routePrefix/Groups/1");

        $response->assertStatus(404);
        $response->assertJsonFragment(['detail' => 'Resource 1 not found']);
    }
}
