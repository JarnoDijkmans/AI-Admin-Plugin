import unittest
import requests
from requests.auth import HTTPBasicAuth
import os;

BASE_URL = os.getenv('BASE_URL', "https://staging-ai.yookerdesign.nl/wp-json/yooker-ai-admin/v1")
USERNAME = "jarno"
KEY = "Bw6nbKFJQb2cS6qdu9QbF8b4"


class TestGetTotalPrice(unittest.TestCase):

    def test_authenticated_user_with_payments(self):
        url = f"{BASE_URL}/get-total-price-spend/"
        auth = HTTPBasicAuth(USERNAME, KEY)

        response = requests.get(url, auth=auth)
        self.assertEqual(response.status_code, 200)

        data = response.json()
        print("Response JSON:", data)

        self.assertIn('status', data, "Response JSON does not contain 'status'")
        self.assertEqual(data['status'], 200)

        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertEqual(data['message'], 'Total cost retrieved successfully')

        self.assertIn('data', data, "Response JSON does not contain 'data'")
        total_cost = float(data['data']) 
        self.assertGreater(total_cost, 0)

    def test_authenticated_user_without_payments(self):
        url = f"{BASE_URL}/get-total-price-spend/"
        auth = HTTPBasicAuth("TestuserWithoutPayments", "3LvGXpjriK4hTx8u6JbOPyMg") 

        response = requests.get(url, auth=auth)
        self.assertEqual(response.status_code, 200)

        data = response.json()
        print("Response status code:", response.status_code)
        print("Response text:", response.text)

        self.assertIn('status', data, "Response JSON does not contain 'status'")
        self.assertEqual(data['status'], 200)

        self.assertIn('message', data, "Response JSON does not contain 'message'")
        self.assertEqual(data['message'], 'No payment history found for this user')

        self.assertIn('data', data, "Response JSON does not contain 'data'")
        self.assertEqual(data['data'], 0) 


    def test_unauthenticated_user(self):
        url = f"{BASE_URL}/get-total-price-spend/"

        response = requests.get(url)  
        self.assertEqual(response.status_code, 401)

        data = response.json()
        self.assertEqual(data['data']['status'], 401)
        self.assertEqual(data['message'], 'Authorization header is missing or invalid')

    def test_malformed_auth(self):
        url = f"{BASE_URL}/get-total-price-spend/"
        auth = HTTPBasicAuth('invalid_username', 'invalid_password') 

        response = requests.get(url, auth=auth)
        self.assertEqual(response.status_code, 401)


if __name__ == "__main__":
    unittest.main()
