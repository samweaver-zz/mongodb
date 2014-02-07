from pymongo import MongoClient
import json
from tweepy.streaming import StreamListener
from tweepy import OAuthHandler
from tweepy import Stream

# Go to http://dev.twitter.com and create an app.
# The consumer key and secret will be generated for you after
consumer_key="e5WLIHyUEdrUW1kfj7CVg"
consumer_secret="lAL8zBeT62zSHjXbetmHX0yw9FZZd4y2cMGcWIgG9Gc"

# After the step above, you will be redirected to your app's page.
# Create an access token under the the "Your access token" section
access_token="74446798-bdOFAzIQUfVJHoE9HrLjM7M6Bv6hhHzAqw6eJ4kjR"
access_token_secret="ZIyOSKW1SzMvW2BWIXZbuawTztcYE6crs9VmDJtaX8"

class StdOutListener(StreamListener):
    """ A listener handles tweets are the received from the stream.
    This is a basic listener that just prints received tweets to stdout.

    """
    def on_data(self, data):
        print data
	tweet = json.loads(data)
	collection.insert(tweet)
        return True

    def on_error(self, status):
        print status

if __name__ == '__main__':

    client = MongoClient()
    db = client.blackhole
    collection = db.singularity

    l = StdOutListener()
    auth = OAuthHandler(consumer_key, consumer_secret)
    auth.set_access_token(access_token, access_token_secret)

    stream = Stream(auth, l)
    stream.filter(track=['apple'])
    #stream.sample()
