<?php

namespace RobTrehy\LaravelAzureProvisioning\Tests;

class UserTest extends TestCase
{
    public function testCanGetUsers()
    {
        $response = $this->get("/$this->routePrefix/Users");

        $response->assertStatus(200);
        $response->assertJsonFragment(["totalResults" => 0]);
    }

    public function testCanPostUser()
    {
        $response = $this->post("/$this->routePrefix/Users", [
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

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "username" => "UserName123",
            "displayname" => "BobIsAmazing",
        ]);
    }

    public function testCanPostEnterpriseUser()
    {
        $response = $this->post("/$this->routePrefix/Users", [
            "UserName" => "UserName222",
            "Active" => true,
            "DisplayName" => "lennay",
            "schemas" => [
                "urn:ietf:params:scim:schemas:extension:enterprise:2.0:User",
                "urn:ietf:params:scim:schemas:core:2.0:User"
            ],
            "externalId" => uniqid(),
            "emails" => [
                [
                    "Primary" => true,
                    "type" => "work",
                    "value" => "testing@bob2.com"
                ]
            ],
            "urn:ietf:params:scim:schemas:extension:enterprise:2.0:User" => [
                "Department" => "bob",
                "Manager" => [ "Value" => "SuzzyQ" ]
            ]
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            "username" => "UserName222",
            "displayname" => "lennay",
            "emails" => [
                [
                    "primary" => true,
                    "type" => "work",
                    "value" => "testing@bob2.com"
                ]
            ]
        ]);
    }

    public function testCanPatchUser()
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

        $response = $this->patch("/$this->routePrefix/Users/1", [
            "schemas" => [
                "urn:ietf:params:scim:api:messages:2.0:PatchOp"
            ],
            "Operations" => [
                [
                    "op" => "replace",
                    "path" => "displayName",
                    "value" => "Ryan3"
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'username' => 'UserName123',
            'displayname' => 'Ryan3'
        ]);
    }

    public function testCanPutUser()
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

        $response = $this->put("/$this->routePrefix/Users/1", [
            "UserName" => "UserNameReplace2",
            "Active" => true,
            "DisplayName" => "BobIsAmazingREPLACED",
            "schemas" => [
                "urn:ietf:params:scim:schemas:extension:enterprise:2.0:User",
                "urn:ietf:params:scim:schemas:core:2.0:User"
            ],
            "externalId" => uniqid(),
            "emails" => [
                [
                    "Primary" => true,
                    "type" => "work",
                    "value" => "testing@bobREPLACE.com"
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            "username" => "UserNameReplace2",
            "active" => true,
            "displayname" => "BobIsAmazingREPLACED",
            "emails" => [
                [
                    "primary" => true,
                    "type" => "work",
                    "value" => "testing@bobREPLACE.com"
                ]
            ]
        ]);
    }

    public function testCanDeleteUser()
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

        $response = $this->delete("/$this->routePrefix/Users/1");

        $response->assertNoContent($status = 204);

        // Now verify the User has gone!
        $response = $this->get("/$this->routePrefix/Users/1");

        $response->assertStatus(404);
        $response->assertJsonFragment(['detail' => 'Resource 1 not found']);
    }
}
