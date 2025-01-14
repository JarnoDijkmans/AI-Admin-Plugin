# import unittest
# import requests
# import os

# BASE_URL = os.getenv('BASE_URL', "https://staging-ai.yookerdesign.nl/wp-json/yooker-ai-admin/v1")


# def get_nonce():
#     nonce_url = f"{BASE_URL}/get-nonce"
#     headers = {
#         "X-Test-Access": "onlyForTestPurpose",  # Pass custom header
#     }
#     response = requests.get(nonce_url, headers=headers)
    
#     # If the response is successful
#     if response.status_code == 200:
#         nonce = response.json().get('nonce')
#         if nonce:
#             return nonce
#         else:
#             raise ValueError("Nonce not found in response.")
#     else:
#         raise Exception(f"Failed to fetch nonce. Status code: {response.status_code}")


# class TestAISubscriberDetails(unittest.TestCase):
#     def test_valid_user(self):
#         nonce = get_nonce()
#         user_id = 34
#         headers = {
#             "Content-Type": "application/json",
#             "X-WP-Nonce": nonce,
#         }
#         subscriber_url = f"{BASE_URL}/ai-subscriber-details/{user_id}"
#         response = requests.get(subscriber_url, headers=headers)

#         self.assertEqual(response.status_code, 200)

#         data = response.json()
#         self.assertEqual(data['data']['status'], 200)
#         self.assertIn('email', data['data']['response'])
#         self.assertIsInstance(data['data']['response']['subscriptions'], list)

#     def test_missing_user_id(self):
#         url = f"{BASE_URL}/ai-subscriber-details/"
#         response = requests.get(url)
#         self.assertEqual(response.status_code, 404)

#     def test_invalid_user(self):
#         nonce = get_nonce()
#         invalid_user_id = 99999 
#         headers = {
#             "Content-Type": "application/json",
#             "X-WP-Nonce": nonce,
#         }
#         url = f"{BASE_URL}/ai-subscriber-details/{invalid_user_id}"
#         response = requests.get(url, headers=headers)

#         self.assertEqual(response.status_code, 200)
#         data = response.json()
#         self.assertEqual(data['data']['status'], 200)
#         self.assertEqual(data['data']['response']['subscriptions'], [])


# if __name__ == "__main__":
#     unittest.main()